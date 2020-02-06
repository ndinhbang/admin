<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUniqueInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropUnique('inventories_supply_id_expense_id_unique');
            $table->dropUnique('inventories_expense_id_index');

            $table->index(['supply_id', 'inventory_take_id', 'inventory_order_id', 'order_id'], 'supply_and_other_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('inventory', function (Blueprint $table) {
        //     $table->unique(['supply_id', 'inventory_order_id'])->change();
        // });
    }
}
