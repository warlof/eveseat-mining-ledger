<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 23:13
 */

namespace Warlof\Seat\MiningLedger\Jobs\Workers;


use Warlof\Seat\MiningLedger\Models\Character\MiningJournal;

class CharacterMiningLedgerUpdate extends EsiBase {


	/**
	 * The contract for the update call. All
	 * update should at least have this function.
	 *
	 * @return mixed
	 */
	public function call() {

		$this->writeJobLog('mining', 'Processing characterID: ' . $this->characterID);

		$result = $this->esi_instance->setVersion( 'v1' )->invoke( 'get', '/characters/{character_id}/mining/', [
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

		return;
	}
}
