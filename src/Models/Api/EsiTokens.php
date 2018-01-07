<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 02/12/2017
 * Time: 00:24
 */

namespace Warlof\Seat\MiningLedger\Models\Api;


use Illuminate\Database\Eloquent\Model;

class EsiTokens extends Model {

    public $timestamps = true;

    public $incrementing = false;

    protected $table = 'warlof_mining_ledger_esi_tokens';

    protected $primaryKey = 'character_id';

    protected $fillable = [
        'character_id', 'scopes', 'access_token', 'refresh_token', 'expires_at', 'active'
    ];

}