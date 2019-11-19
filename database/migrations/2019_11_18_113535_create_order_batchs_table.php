<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBatchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_batchs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('place_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->smallInteger('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_batchs');
    }
}
