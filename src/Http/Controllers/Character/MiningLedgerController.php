<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 21:54
 */

namespace Warlof\Seat\MiningLedger\Http\Controllers\Character;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Seat\Web\Http\Controllers\Controller;
use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;
use Warlof\Seat\MiningLedger\Models\Character\MiningJournal;

class MiningLedgerController extends Controller {

    public function getLedger(int $character_id) : View
    {
        $token  = EsiTokens::find($character_id);
        $ledger = MiningJournal::select('date', 'solar_system_id', 'type_id', DB::raw('SUM(quantity) as quantity'))
                               ->where('character_id', $character_id)
                               ->groupBy('date', 'solar_system_id', 'type_id')
                               ->get();

        return view('mining-ledger::character.ledger', compact('ledger', 'token'));
    }

    public function getDetailedLedger(int $character_id, $date, int $system_id, int $type_id) : JsonResponse
    {
        $entries = MiningJournal::select('time', 'type_id', 'quantity')
                                ->where('character_id', $character_id)
                                ->where('date', $date)
                                ->where('solar_system_id', $system_id)
                                ->where('type_id', $type_id)
                                ->get();

        return response()->json($entries);
    }

}
