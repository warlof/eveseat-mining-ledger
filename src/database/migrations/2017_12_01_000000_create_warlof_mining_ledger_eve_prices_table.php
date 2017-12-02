<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 22:41
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarlofMiningLedgerEvePricesTable extends Migration {

	public function up()
	{
		Schema::create('warlof_mining_ledger_eve_prices', function (Blueprint $table) {

			$table->bigInteger('type_id');
			$table->decimal('average_price',30,2)->default(0.0);
			$table->decimal('adjusted_price',30,2)->default(0.0);

			$table->primary(['type_id']);

		});
	}

	public function down()
	{
		if (Schema::hasTable('warlof_mining_ledger_eve_prices')) {
			Schema::drop('warlof_mining_ledger_eve_prices');
		}
	}

}
