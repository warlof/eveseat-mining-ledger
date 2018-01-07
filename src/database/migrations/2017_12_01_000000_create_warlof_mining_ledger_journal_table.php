<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 22:41
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarlofMiningLedgerJournalTable extends Migration {

    public function up()
    {
        Schema::create('warlof_mining_ledger_character_mining_journal', function (Blueprint $table) {

            $table->bigInteger('character_id');
            $table->date('date');
            $table->bigInteger('solar_system_id');
            $table->bigInteger('type_id');
            $table->integer('quantity');

            $table->primary(['character_id', 'date', 'solar_system_id', 'type_id'], 'ledger_primary_key');

        });
    }

    public function down()
    {
        if (Schema::hasTable('warlof_mining_ledger_character_mining_journal')) {
            Schema::drop('warlof_mining_ledger_character_mining_journal');
        }
    }

}