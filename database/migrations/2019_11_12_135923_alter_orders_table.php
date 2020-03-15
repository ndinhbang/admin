<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('state_id')->unsigned()->default(0)->after('place_id')->change();
            $table->integer('amount')->default(0)->after('state_id');
            $table->integer('customer_id')->unsigned()->default(0)->index()->after('place_id'); // id khach hang
            $table->integer('cashier_id')->unsigned()->default(0)->index()->after('place_id'); // id thu ngan
            $table->boolean('is_paid')->default(0)->after('amount');
            $table->boolean('is_finished')->default(0)->after('amount'); // danh dau don hang da hoan thanh
            $table->boolean('is_canceled')->default(0)->after('amount'); // don hang bi huy
            $table->boolean('is_returned')->default(0)->after('amount'); // don hang bi tra lai
            $table->date('on_date')->after('is_returned'); // don hang ta vao ngay
            $table->timestamp('entranced_at')->nullable()->after('on_date');
            $table->timestamp('left_at')->nullable()->after('on_date');
            // tong tien hang
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
            $table->dropColumn([
                'customer_id',
                'cashier_id',
                'is_paid',
                'is_finished',
                'is_canceled',
                'is_returned',
                'on_date',
                'entranced_at',
                'left_at',
                'amount',
            ]);
        });
    }
}
