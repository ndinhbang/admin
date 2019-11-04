<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('title')->after('name');
            $table->unsignedInteger('place_id')->after('id')->default(0);
            $table->boolean('forbidden')->default(false);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->string('title')->after('name');
            $table->unsignedInteger('place_id')->after('id')->default(0);
            $table->unsignedInteger('level')->after('id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropColumn(['title', 'scope', 'forbidden']);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn(['title', 'scope', 'level']);
        });
    }
}
