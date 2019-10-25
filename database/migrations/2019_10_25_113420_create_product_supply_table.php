<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSupplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_supply', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('supply_id')->unsigned()->index();
            $table->double('quantity', 6,2)->unsigned()->default(1);
            $table->primary(['product_id', 'supply_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_supply');
    }
}
