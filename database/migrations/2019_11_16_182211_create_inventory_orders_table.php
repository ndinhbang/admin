<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 21)->unique();
            $table->char('code', 20);
            $table->integer('place_id')->unsigned()->index();
            $table->integer('creator_id');
            $table->integer('supplier_id');
            $table->integer('user_id');
            $table->float('amount', 10, 2)->default(0); // tổng số tiền
            $table->float('debt', 10, 2)->default(0); // số tiền nợ
            $table->float('paid', 10, 2)->default(0); // số tiền đã trả
            $table->tinyInteger('type')->default(1);
            // 1: Đơn nhập
            // 0: Đơn trả nhà Cung cấp

            $table->dateTime('on_date');
            $table->string('note', 500)->nullable(); // ghi chu
            $table->string('attached_files', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->unique(['place_id', 'code']);
        });

        Schema::rename('inventories', 'inventory');

        Schema::table('inventory', function (Blueprint $table) {
            $table->renameColumn('order_id', 'inventory_order_id');
            $table->float('price_pu', 10, 2)->default(0)->after('total_price'); // price per unit giá/ 1 đơn vị
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_orders');
        
        Schema::rename('inventory', 'inventories');

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('price_pu');
            $table->renameColumn('inventory_order_id', 'order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
