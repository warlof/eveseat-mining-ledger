<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 22:41
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateWarlofMiningLedgerJournalTableAddTime extends Migration {

    public function up()
    {
        Schema::table('warlof_mining_ledger_character_mining_journal', function (Blueprint $table) {

            $table->time('time')->after('date');

            $table->dropPrimary('ledger_primary_key');
            $table->primary(['character_id', 'date', 'time', 'solar_system_id', 'type_id'], 'ledger_primary_key');
            $table->index('character_id', 'ledger_character');
            $table->index('solar_system_id', 'ledger_system');
            $table->index('type_id', 'ledger_type');
            $table->index(['character_id', 'date', 'solar_system_id', 'type_id'], 'ledger_character_date_systype');

        });
    }

    public function down()
    {
        if (Schema::hasTable('warlof_mining_ledger_character_mining_journal')) {
            Schema::table('warlof_mining_ledger_character_mining_journal', function(Blueprint $table) {

                // retrieve all existing records, group them by character, date, system and type
                $rows = DB::table('warlof_mining_ledger_character_mining_journal')
                    ->select(DB::raw('SUM(quantity) as quantity'))
                    ->groupBy('character_id')
                    ->groupBy('date')
                    ->groupBy('solar_system_id')
                    ->groupBy('type_id')
                    ->get();

                // foreach retrieved aggregated records, spawn a new unique record with midnight time
                // (or update any existing one)
                foreach ($rows as $row) {

                    $isRecordExists = DB::table('warlof_mining_ledger_character_mining_journal')
                                        ->where('character_id', $row->character_id)
                                        ->where('date', $row->date)
                                        ->where('time', '00:00:00')
                                        ->where('solar_system_id', $row->solar_system_id)
                                        ->where('type_id', $row->type_id)
                                        ->count();

                    if ($isRecordExists > 0) {

                        DB::table('warlof_mining_ledger_character_mining_journal')
                            ->where('character_id', $row->character_id)
                            ->where('date', $row->date)
                            ->where('time', '00:00:00')
                            ->where('solar_system_id', $row->solar_system_id)
                            ->where('type_id', $row->type_id)
                            ->update([
                                'quantity' => $row->quantity,
                            ]);

                        continue;
                    }

                    DB::table('warlof_mining_ledger_character_mining_journal')->insert([
                        'character_id' => $row->character_id,
                        'date' => $row->date,
                        'time' => '00:00:00',
                        'solar_system_id' => $row->solar_system_id,
                        'type_id' => $row->type_id,
                        'quantity' => $row->quantity,
                    ]);

                }

                // drop all records which have not been spawn by migration
                DB::table('warlof_mining_ledger_character_mining_journal')->where('time', '<>', '00:00:00')->delete();

                // drop indexes
                $table->dropPrimary('ledger_primary_key');
                $table->dropIndex('character_id', 'ledger_character');
                $table->dropIndex('solar_system_id', 'ledger_system');
                $table->dropIndex('type_id', 'ledger_type');
                $table->dropIndex(['character_id', 'date', 'solar_system_id', 'type_id'], 'ledger_character_date_systype');

                // remove time column
                $table->dropColumn('time');

                // renew primary key
                $table->primary(['character_id', 'date', 'solar_system_id', 'type_id'], 'ledger_primary_key');
            });
        }
    }

}
