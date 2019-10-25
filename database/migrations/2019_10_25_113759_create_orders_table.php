<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 21)->unique();
            $table->integer('place_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->tinyInteger('state_id')->default(0)->unsigned();
            $table->tinyInteger('is_finised')->default(0); // 1: da xong, -1: huy
            $table->string('note')->default(''); // ghi chu
            $table->string('reason')->default('');
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
        Schema::dropIfExists('orders');
    }
}
