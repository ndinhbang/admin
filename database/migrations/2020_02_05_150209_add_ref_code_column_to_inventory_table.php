<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefCodeColumnToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->char('ref_code', 20)->after('id');
            $table->boolean('status')->default(false)->after('note');

            $table->integer('order_id')->nullable()->default(0)->after('ref_code');
            $table->integer('inventory_take_id')->nullable()->default(0)->after('order_id');
            $table->integer('inventory_order_id')->nullable()->default(0)->change();
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->double('remain', 10, 2)->default(0)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn(['ref_code', 'status', 'order_id', 'inventory_take_id']);
        });
        
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn(['remain']);
        });
    }
}
