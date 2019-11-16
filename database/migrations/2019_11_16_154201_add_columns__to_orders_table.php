<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('payer_payee_id', 'customer_id');
            $table->dropColumn(['on_date','entranced_at', 'left_at', 'type']);
            $table->unsignedSmallInteger('year')->after('state');
            $table->unsignedTinyInteger('month')->after('state');
            $table->unsignedTinyInteger('day')->after('state');
            $table->boolean('is_completed')->default(0)->after('is_paid');
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
            $table->renameColumn('customer_id', 'payer_payee_id');
            $table->date('on_date')->after('is_returned');
            $table->timestamp('entranced_at')->after('is_returned')->nullable();
            $table->timestamp('left_at')->after('is_returned')->nullable();
            $table->tinyInteger('type')->default(1)->after('reason');
            $table->dropColumn(['year', 'month', 'day', 'is_completed']);
        });
    }
}
