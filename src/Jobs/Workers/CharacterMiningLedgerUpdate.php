<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 23:13
 */

namespace Warlof\Seat\MiningLedger\Jobs\Workers;


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
                MiningJournal::updateOrCreate([
                    'character_id' => $this->getCharacterID(),
                    'date' => $entry->date,
                    'solar_system_id' => $entry->solar_system_id,
                    'type_id' => $entry->type_id,
                ], [
                    'quantity' => $entry->quantity,
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
