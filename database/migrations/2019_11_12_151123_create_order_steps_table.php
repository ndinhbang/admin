<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 21)->unique();
            $table->string('name', 50);
            $table->boolean('is_pending')->default(0);
            $table->boolean('is_accepted')->default(0); // bao bep
            $table->boolean('is_doing')->default(0); //  dang lam
            $table->boolean('is_done')->default(0); //  da lam xong
//            $table->boolean('is_served')->default(0); // da phuc vu
            $table->boolean('is_delivered')->default(0); // da giao toi khach
            $table->boolean('is_paid')->default(0); //  da thanh toan
            $table->boolean('is_finsihed')->default(0); // da hoan thanh
            $table->tinyInteger('num'); // so thu tu cua step
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_steps');
    }
}
