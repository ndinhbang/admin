<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryOrderIdColumnToVouchersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('vouchers', function (Blueprint $table) {
			$table->integer('inventory_order_id')->default(0)->after('order_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('vouchers', function (Blueprint $table) {
			$table->dropColumn('inventory_order_id');
		});
	}
}
