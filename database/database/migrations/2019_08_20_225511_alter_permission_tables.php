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
            $table->char('uuid', 21)->unique()->after('id');
            $table->string('title')->after('name');
            $table->unsignedInteger('place_id')->after('id')->default(0);
//            $table->boolean('forbidden')->after('name')->default(false);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->char('uuid', 21)->unique()->after('id');
            $table->string('title')->after('name');
            $table->unsignedInteger('place_id')->after('id')->default(0);
            $table->unsignedInteger('level')->after('name')->default(0);
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
            $table->dropColumn(['uuid', 'title', 'place_id']);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn(['uuid', 'title', 'place_id', 'level']);
        });
    }
}
