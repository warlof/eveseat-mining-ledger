<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 21:54
 */

namespace Warlof\Seat\MiningLedger\Http\Controllers\Character;


use Illuminate\View\View;
use Seat\Web\Http\Controllers\Controller;
use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;
use Warlof\Seat\MiningLedger\Models\Character\MiningJournal;

class MiningLedgerController extends Controller {

    public function getLedger(int $character_id) : View
    {
        $token  = EsiTokens::find($character_id);
        $ledger = MiningJournal::where('character_id', $character_id)->get();

        return view('mining-ledger::character.ledger', compact('ledger', 'token'));
    }

}
