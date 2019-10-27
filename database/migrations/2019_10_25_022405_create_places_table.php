<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191)->nullable();
            $table->string('code',64)->unique();
            $table->string('logo', 255)->nullable();
            $table->string('contact_name', 191)->nullable();
            $table->string('contact_phone', 191)->nullable();
            $table->string('contact_email', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->enum('status', ['trial', 'premium'])->default('trial');
            $table->date('expired_date')->nullable();
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('places');
    }
}
