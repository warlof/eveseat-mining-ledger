<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 21:15
 */

namespace Warlof\Seat\MiningLedger\Models\Sde;


use Illuminate\Database\Eloquent\Model;
use Warlof\Seat\MiningLedger\Models\Eve\ItemMarketPrice;

class InvType extends Model {

	public $timestamps = false;

	public $incrementing = false;

	protected $table = 'invTypes';

	protected $primaryKey = 'typeID';

	public function prices()
	{
		return $this->hasOne(ItemMarketPrice::class, 'type_id', 'typeID');
	}

}