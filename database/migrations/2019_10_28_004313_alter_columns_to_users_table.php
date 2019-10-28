<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->unique()->nullable(0)->change();
            $table->string('phone')->unique()->nullable(1)->change();
            
            $table->string('email')->nullable(1)->change();
            $table->string('status', 20)->default('activated')->after('password');
            $table->tinyInteger('admin')->unsigned()->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_name_unique');
            $table->dropUnique('users_phone_unique');
            $table->string('name')->nullable(0)->change();
            $table->string('phone')->nullable(0)->change();
            $table->string('email')->nullable(0)->change();
            $table->dropColumn('admin');
            $table->dropColumn('status');
        });
    }
}
