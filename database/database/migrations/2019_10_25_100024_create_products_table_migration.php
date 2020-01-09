<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTableMigration extends Migration {
	public function up() {
		Schema::create('products', function (Blueprint $table) {
			$table->increments('id');
			$table->char('uuid', 21)->unique();
			$table->integer('place_id')->unsigned()->index(); // id cua hang
			$table->integer('category_id')->index()->unsigned()->default(0);
			$table->integer('position')->unsigned()->default(0); // vi tri sap xep
			$table->string('name');
			$table->double('price', 10, 2); // gia ban
			$table->string('thumbnail')->default('');
			$table->string('description')->nullable();
			$table->boolean('is_hot')->default(0);
			$table->boolean('state')->default(0); // 1: mo ban, 0: dung ban
			$table->timestamps();
		});

	}

	public function down() {
		Schema::dropIfExists('products');
	}
}
