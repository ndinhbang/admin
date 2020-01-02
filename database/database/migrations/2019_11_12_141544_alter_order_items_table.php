<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('discount_amount')->unsigned()->default(0)->after('total_price');
            $table->integer('state_id')->unsigned()->default(0)->after('total_price');
            $table->boolean('is_done')->default(0)->after('total_price');   // da pha che / lam xong
            $table->boolean('is_delivered')->default(0)->after('total_price'); // da phuc vu toi khac hang
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'state_id',
                'is_done',
                'is_delivered',
                'discount_amount',
            ]);
        });
    }
}
