<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameExpensesToVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('expenses');

        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 21)->unique();
            $table->char('code', 20)->unique();
            $table->tinyInteger('type')->default(0); // 0: Chi | 1: Thu
            $table->string('title')->nullable();
            $table->string('note')->nullable();
            $table->char('payment_method', 10)->default('cash');
            $table->string('attachments')->default('');
            $table->integer('amount');
            $table->boolean('state')->default(1); // trang thai phe duyet
            $table->timestamp('imported_at');
            $table->integer('category_id')->unsigned()->index();
            $table->integer('creator_id')->unsigned();
            $table->integer('payer_payee_id')->unsigned();
            $table->integer('approver_id')->unsigned()->default(0);
            $table->integer('place_id')->unsigned()->index();
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
        Schema::dropIfExists('vouchers');

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

        Schema::table('vouchers', function (Blueprint $table) {
            //
        });
    }
}
