<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 02/12/2017
 * Time: 17:22
 */

namespace Warlof\Seat\MiningLedger\Models\Eve;


use Illuminate\Database\Eloquent\Model;
use Warlof\Seat\MiningLedger\Models\Sde\InvType;

class ItemMarketPrice extends Model {

	public $timestamps = false;

	public $incrementing = false;

	protected $table = 'warlof_mining_ledger_eve_prices';

	protected $primaryKey = 'type_id';

	protected $fillable = [
		'type_id', 'average_price', 'adjusted_price',
	];

	public function type()
	{
		return $this->hasOne(InvType::class, 'itemID', 'type_id');
	}

}
