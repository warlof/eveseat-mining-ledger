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

class UpdateWarlofMiningLedgerJournalTableAddTimeIndexes extends Migration {

    public function up()
    {
        Schema::table('warlof_mining_ledger_character_mining_journal', function (Blueprint $table) {

            $table->integer('year')->after('time');
            $table->integer('month')->after('year');

            $table->index('year');
            $table->index(['year', 'month']);
        });

        DB::update('UPDATE warlof_mining_ledger_character_mining_journal SET `year` = YEAR(`date`), `month` = MONTH(`date`)');

        // prune all records which have been added since release 2.0.13
        DB::table('warlof_mining_ledger_character_mining_journal')
          ->where('date', '>=', '2018/06/03')
          ->delete();

        // prune all records with an empty value
        DB::table('warlof_mining_ledger_character_mining_journal')
          ->where('quantity', 0)
          ->delete();

        // prune all records with a time different from midnight (corresponding to 2.0.13 migrations)
        DB::table('warlof_mining_ledger_character_mining_journal')
          ->where('time', '<>', '00:00:00')
          ->delete();
    }

    public function down()
    {
        if (Schema::hasTable('warlof_mining_ledger_character_mining_journal')) {
            Schema::table('warlof_mining_ledger_character_mining_journal', function(Blueprint $table) {

                // drop indexes
                $table->dropIndex('year');
                $table->dropIndex(['year', 'month']);

                // remove columns
                $table->dropColumn('month');
                $table->dropColumn('year');
            });
        }
    }

}
