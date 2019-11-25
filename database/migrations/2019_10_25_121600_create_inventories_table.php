<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inventories', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('expense_id')->unsigned()->index();
			$table->integer('supply_id')->unsigned();
			$table->integer('total_price')->unsigned();
			$table->double('quantity', 6, 2)->unsigned();
			$table->double('remain', 6, 2)->unsigned();
			$table->timestamps();

			$table->unique(['supply_id', 'expense_id']);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('inventories');
	}
}
