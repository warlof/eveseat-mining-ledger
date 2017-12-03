<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 20:42
 */

namespace Warlof\Seat\MiningLedger\Models\Character;


use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Api\Character\CharacterSheet;
use Warlof\Seat\MiningLedger\Models\Sde\InvType;
use Warlof\Seat\MiningLedger\Models\Sde\MapDenormalize;

class MiningJournal extends Model {

	public $timestamps = false;

	public $incrementing = false;

	protected $primaryKey = ['character_id', 'date', 'solar_system_id', 'type_id'];

	protected $table = 'warlof_mining_ledger_character_mining_journal';

	protected $fillable = [
		'character_id', 'date', 'solar_system_id', 'type_id', 'quantity',
	];

	public function character()
	{
		return $this->belongsTo(CharacterSheet::class, 'character_id', 'characterID');
	}

	public function type()
	{
		return $this->hasOne(InvType::class, 'typeID', 'type_id');
	}

	public function system()
	{
		return $this->hasOne(MapDenormalize::class, 'itemID', 'solar_system_id');
	}
}
