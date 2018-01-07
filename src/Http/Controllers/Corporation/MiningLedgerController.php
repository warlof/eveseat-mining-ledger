<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 21:53
 */

namespace Warlof\Seat\MiningLedger\Http\Controllers\Corporation;


use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Seat\Web\Http\Controllers\Controller;
use Warlof\Seat\MiningLedger\Models\Corporation\MemberTracking;

class MiningLedgerController extends Controller {

    public function getLedger(int $corporation_id, int $year = null, int $month = null) : View
    {
        if (is_null($year))
            $year = date('Y');

        if (is_null($month))
            $month = date('m');

        $ledgers = DB::table('warlof_mining_ledger_character_mining_journal')
                     ->select(DB::raw('DISTINCT YEAR(date) as year, MONTH(date) as month'))
                     ->whereIn('character_id', MemberTracking::where('corporationID', $corporation_id)
                                                             ->select('characterID')->get())
                     ->orderBy('year', 'desc')
                     ->orderBy('month', 'desc')
                     ->get();

        $entries = DB::table('warlof_mining_ledger_character_mining_journal')
            ->select('character_id', 'name', DB::raw('year(date) as year'), DB::raw('month(date) as month'),
                DB::raw('sum(quantity) as quantities'), DB::raw('sum(quantity * volume) as volumes'),
                DB::raw('sum(quantity * average_price) as amounts'))
            ->join('warlof_mining_ledger_eve_prices', 'warlof_mining_ledger_eve_prices.type_id', 'warlof_mining_ledger_character_mining_journal.type_id')
            ->join('invTypes', 'typeID', 'warlof_mining_ledger_character_mining_journal.type_id')
            ->join('corporation_member_trackings', 'characterID', 'character_id')
            ->where('corporationID', $corporation_id)
            ->groupBy('character_id', 'name', 'year', 'month')
            ->having('year', '=', $year)
            ->having('month', '=', $month)
            ->get();

        return view('mining-ledger::corporation.views.ledger', compact('ledgers', 'entries'));
    }

    public function getTracking(int $corporation_id) : View
    {
        $members = MemberTracking::where('corporationID', $corporation_id)->get();

        return view('mining-ledger::corporation.views.tracking', compact('members'));
    }

}
