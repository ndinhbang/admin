<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dateTime('last_order_at')->nullable()->after('type');
            $table->float('total_return_amount', 10, 2)->default(0)->after('type'); // tổng số tiền trả hàng / trả lại nhà ncc
            $table->float('total_debt', 10, 2)->default(0)->after('type'); // tổng số tiền khách nợ / nợ ncc
            $table->float('total_amount', 10, 2)->default(0)->after('type'); // tổng số tiền khách mua hàng / nhập hàng từ ncc
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('last_order_at');
            $table->dropColumn('total_amount');
            $table->dropColumn('total_return_amount');
            $table->dropColumn('total_debt');
        });
    }
}
