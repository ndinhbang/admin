<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 21)->unique();
            $table->string('code', 20);
            $table->integer('place_id');
            $table->integer('user_id')->nullable();
            $table->string('name', 100);
            $table->string('unsigned_name');
            $table->string('contact_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->tinyInteger('birth_month')->default(0);
            $table->date('birth_day')->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('is_corporate')->default(0); // Persional
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('note', 500)->nullable();
            $table->enum('type', ['customer', 'supplier', 'shipper', 'employee']);
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
        Schema::dropIfExists('accounts');
    }
}
