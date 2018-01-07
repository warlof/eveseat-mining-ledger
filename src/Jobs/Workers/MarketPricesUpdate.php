<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 02/12/2017
 * Time: 17:15
 */

namespace Warlof\Seat\MiningLedger\Jobs\Workers;


use Seat\Eseye\Exceptions\EsiScopeAccessDeniedException;
use Seat\Eseye\Exceptions\RequestFailedException;
use Warlof\Seat\MiningLedger\Models\Eve\ItemMarketPrice;

class MarketPricesUpdate extends EsiBase {

    /**
     * The contract for the update call. All
     * update should at least have this function.
     *
     * @throws EsiScopeAccessDeniedException
     * @throws RequestFailedException
     * @return mixed
     */
    public function call() {

        $this->writeJobLog('mining', 'Processing characterID: ' . $this->characterID);

        $result = $this->esi_instance->setVersion( 'v1' )->invoke( 'get', '/markets/prices/');

        foreach ($result as $entry) {
            ItemMarketPrice::updateOrCreate([
                'type_id' => $entry->type_id,
            ], [
                'average_price'  => property_exists($entry, 'average_price') ? $entry->average_price : 0.0,
                'adjusted_price' => property_exists($entry, 'adjusted_price') ? $entry->adjusted_price : 0.0,
            ]);
        }

        return;
    }

}
