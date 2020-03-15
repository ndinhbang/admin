<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('segments', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->char('uuid', 21)->unique();
            $t->unsignedBigInteger('place_id');
            $t->string('title');
            $t->string('description')->nullable();
            $t->timestamps();
        });

        \Schema::create('segments_accounts',function (Blueprint $t){
            $t->bigIncrements('id');
            $t->unsignedBigInteger('segment_id');
            $t->unsignedBigInteger('account_id');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('segments');
        Schema::dropIfExists('segments_accounts');
    }
}
