<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 21)->unique();
            $table->integer('place_id')->unsigned()->index();
            $table->string('title');
            $table->string('note')->default('');
            $table->string('invoice')->default('');
            $table->integer('amount');
            $table->boolean('state')->default(0); // trang thai phe duyet
            $table->timestamp('imported_at');
            $table->integer('category_id')->unsigned()->index();
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
        Schema::dropIfExists('expenses');
    }
}
