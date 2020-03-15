<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToInventoryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_orders', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0);
            // 1: Hoàn thành
            // 0: Lưu tạm
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
