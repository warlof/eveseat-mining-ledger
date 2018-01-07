<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 21:26
 */

namespace Warlof\Seat\MiningLedger\Models\Sde;


use Illuminate\Database\Eloquent\Model;

class MapDenormalize extends Model {

    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'mapDenormalize';

    protected $primaryKey = 'itemID';

}
