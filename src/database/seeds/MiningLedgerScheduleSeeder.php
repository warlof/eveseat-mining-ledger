<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 03/12/2017
 * Time: 00:16
 */

namespace Warlof\Seat\MiningLedger\database\seeds;


use Illuminate\Database\Seeder;
use Seat\Services\Models\Schedule;

class MiningLedgerScheduleSeeder extends Seeder {

    public function run()
    {
        if(!Schedule::where('command', 'esi:market-prices:update')->first()) {
            // update market prices twice a day
            Schedule::create([
                'command' => 'esi:market-prices:update',
                'expression' => '* */12 * * *',
            ]);
        }

        if (!Schedule::where('command', 'esi:mining-ledger:update')->first()) {
            // update mining ledger every 15 minutes.
            // cache is 10, avoid exception due to token expiration when the call is started
            Schedule::create([
                'command' => 'esi:mining-ledger:update',
                'expression' => '*/15 * * * *',
            ]);
        }
    }

}
