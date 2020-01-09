<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnStateIdToStepId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('state_id', 'step_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('state_id', 'step_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('step_id', 'state_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('step_id', 'state_id');
        });
    }
}
