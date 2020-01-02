<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('place_id');
            $t->char('uuid', 21);
            $t->string('title');
            $t->string('description')->nullable();
            $t->string('code');
            $t->dateTime('start_date');
            $t->dateTime('end_date');
            $t->integer('quantity');
            $t->boolean('require_coupon');
            $t->enum('type', ['product', 'order']);
            $t->timestamps();
        });

        Schema::create('promotions_customers', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('promotion_id');

            $t->unsignedBigInteger('segment_id');
            $t->unsignedBigInteger('customer_id');

        });

        Schema::create('promotion_applieds', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('promotion_id');

            $t->enum('type', ['product', 'order']);

            $t->unsignedBigInteger('category_id')->nullable();
            $t->unsignedBigInteger('product_id')->nullable();


            $t->bigInteger('quantity');
            $t->bigInteger('discount');
            $t->enum('unit', ['percent', 'money']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
