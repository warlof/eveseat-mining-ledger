<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 22:41
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarlofMiningLedgerEsiTokensTable extends Migration {

	public function up()
	{
		Schema::create('warlof_mining_ledger_esi_tokens', function (Blueprint $table) {

			$table->bigInteger('character_id');
			$table->string('scopes');
			$table->string('access_token')->nullable();
			$table->string('refresh_token')->nullable();
			$table->boolean('active')->default(true);
			$table->dateTime('expires_at')->nullable();

			$table->timestamps();

			$table->primary(['character_id']);

		});
	}

	public function down()
	{
		if (Schema::hasTable('warlof_mining_ledger_esi_tokens')) {
			Schema::drop('warlof_mining_ledger_esi_tokens');
		}
	}

}