<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdColumnToSuppliesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('supplies', function (Blueprint $table) {
			$table->unsignedInteger('unit_id')->default(0)->after('place_id');
			$table->unsignedInteger('min_stock')->default(0)->after('price_in');
			$table->unsignedInteger('max_stock')->default(0)->after('min_stock');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('supplies', function (Blueprint $table) {
			$table->dropColumn('unit_id');
			$table->dropColumn('min_stock');
			$table->dropColumn('max_stock');
		});
	}
}
