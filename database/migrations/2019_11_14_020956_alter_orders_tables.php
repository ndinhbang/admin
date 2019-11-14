<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->char('code', 20)->unique()->after('uuid');
            $table->float('debt', 10, 2)->default(0)->after('amount'); // số tiền nợ
            $table->float('paid', 10, 2)->default(0)->after('debt'); // số tiền đã trả
            $table->integer('quantity')->default(0)->after('paid'); // số món
            $table->tinyInteger('type')->default(1)->after('reason');
            // 0: Đơn xuất ( bán )
            // 1: Đơn nhập
            // 2: Đơn khách trả hàng
            // 3: Đơn trả nhà Cung cấp

            $table->string('attached_files', 255)->after('reason')->nullable();

            $table->renameColumn('cashier_id', 'creator_id');
            $table->renameColumn('customer_id', 'payer_payee_id');

            $table->unique(['place_id', 'code']);
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->float('price_in', 10, 2)->default(0)->after('name');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('expense_id', 'order_id');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->integer('order_id')->default(0)->after('imported_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');

        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn('price_in');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('purchase_id', 'expense_id');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('purchase_id');
        });
    }
}
