<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 02/12/2017
 * Time: 16:47
 */

namespace Warlof\Seat\MiningLedger\Models\Corporation;


use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;

class MemberTracking extends \Seat\Eveapi\Models\Corporation\MemberTracking {

    public function token()
    {
        return $this->hasOne(EsiTokens::class, 'character_id', 'characterID');
    }

}
