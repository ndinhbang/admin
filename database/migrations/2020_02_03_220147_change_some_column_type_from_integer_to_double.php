<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSomeColumnTypeFromIntegerToDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // inventory
        DB::statement('ALTER TABLE inventory MODIFY total_price DOUBLE(12,2) DEFAULT 0;');
        DB::statement('ALTER TABLE inventory MODIFY qty_import DOUBLE(6,2) DEFAULT 0;');
        DB::statement('ALTER TABLE inventory MODIFY qty_export DOUBLE(6,2) DEFAULT 0;');
        DB::statement('ALTER TABLE inventory MODIFY qty_remain DOUBLE(6,2) DEFAULT 0;');

        // inventory_takes
        DB::statement('ALTER TABLE inventory_takes MODIFY qty DOUBLE(6,2) DEFAULT 0;');
        DB::statement('ALTER TABLE inventory_takes MODIFY qty_diff DOUBLE(6,2) DEFAULT 0;');
        DB::statement('ALTER TABLE inventory_takes MODIFY qty_excessing DOUBLE(6,2) DEFAULT 0;');
        DB::statement('ALTER TABLE inventory_takes MODIFY qty_missing DOUBLE(6,2) DEFAULT 0;');

        // orders
        DB::statement('ALTER TABLE orders MODIFY amount DOUBLE(12,2) DEFAULT 0;');

        // order_items
        DB::statement('ALTER TABLE order_items MODIFY quantity DOUBLE(6,2) DEFAULT 1;');
        DB::statement('ALTER TABLE order_items MODIFY total_price DOUBLE(12,2) DEFAULT 0;');
        DB::statement('ALTER TABLE order_items MODIFY discount_amount DOUBLE(12,2) DEFAULT 0;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change(); // Phút sử dụng
        });
    }
}
