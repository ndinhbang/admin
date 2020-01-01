<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCriteria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segments_criteria', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('uuid',21);
            $t->unsignedBigInteger('place_id');
            $t->unsignedBigInteger('segment_id');
            $t->enum('property',['total_amount','total_paid','total_debt','birth_month','last_order','gender']);
            $t->enum('operator',['=','>','>=','<','<=','!='])->default('=');
            $t->bigInteger('value')->default(0);
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
        Schema::dropIfExists('segments_criteria');
    }
}
