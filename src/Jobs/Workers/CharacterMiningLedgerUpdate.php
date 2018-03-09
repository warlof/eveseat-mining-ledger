<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 23:13
 */

namespace Warlof\Seat\MiningLedger\Jobs\Workers;


use Illuminate\Support\Facades\DB;
use Seat\Eseye\Exceptions\EsiScopeAccessDeniedException;
use Seat\Eseye\Exceptions\InvalidContainerDataException;
use Seat\Eseye\Exceptions\RequestFailedException;
use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;
use Warlof\Seat\MiningLedger\Models\Character\MiningJournal;

class CharacterMiningLedgerUpdate extends EsiBase {


    /**
     * The contract for the update call. All
     * update should at least have this function.
     *
     * @throws InvalidContainerDataException
     * @throws EsiScopeAccessDeniedException
     * @throws RequestFailedException
     * @return mixed
     */
    public function call() {

        $this->writeJobLog('mining', 'Processing characterID: ' . $this->characterID);

        try {

            $result = $this->esi_instance->setVersion('v1')->invoke('get', '/characters/{character_id}/mining/', [
                'character_id' => $this->getCharacterID(),
            ]);

            foreach ($result as $entry) {

                // retrieve daily mined amount for current type, system and character
                $row = MiningJournal::select(DB::raw('SUM(quantity) as quantity'))
                                    ->where('character_id', $this->getCharacterID())
                                    ->where('date', $entry->date)
                                    ->where('solar_system_id', $entry->solar_system_id)
                                    ->where('type_id', $entry->type_id)
                                    ->first();

                // get the current UTC time for potential new mining ledger entry
                $delta_time = carbon()->setTimezone('UTC')->toTimeString();

                // compute delta between daily knew mined amount and new daily mined amount
                $delta_quantity = $entry->quantity - (is_null($row) ? 0 : $row->quantity);

                // in case delta is 0, we skip the entry since there are no needs to store empty value
                if ($delta_quantity == 0)
                    continue;

                // in case the entry date does not match with the current date, we reset entry time to midnight
                // as a last entry
                if ($entry->date != carbon()->setTimezone('UTC')->toDateString())
                    $delta_time = '23:59:59';

                // create a new ledger entry with computed information
                MiningJournal::updateOrCreate([
                    'character_id'    => $this->getCharacterID(),
                    'date'            => $entry->date,
                    'time'            => $delta_time,
                    'solar_system_id' => $entry->solar_system_id,
                    'type_id'         => $entry->type_id,
                ], [
                    'quantity'        => $delta_quantity,
                ]);

            }

            $this->updateEsiToken();

        } catch (RequestFailedException $e) {

            logger()->error(print_r($e->getEsiResponse(), true));

            // in case the character does not exists, drop the token from the system
            // and end the job
            if ($e->getEsiResponse()->getErrorCode() == 404) {
                EsiTokens::find($this->getCharacterID())->delete();
                return;
            }

            // in case the refresh token has been reset, drop it from the system
            // and end the job
            if ($e->getEsiResponse()->error == 'invalid_token') {
                EsiTokens::find($this->getCharacterID())->delete();
                return;
            }

            // for any other error, forward the exception;
            throw $e;

        }

        return;
    }
}
