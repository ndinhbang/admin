<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrderBatchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_batchs', function (Blueprint $table) {
            $table->boolean('is_canceled')->default(0)->after('quantity');
            $table->tinyInteger('state')->default(0)->after('quantity');
            $table->string('reason')->default('')->after('state');
            $table->string('note')->default('')->after('state');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('is_served');
            $table->dropColumn('is_done');
            $table->dropColumn('is_canceled');
            $table->dropColumn('reason');
            $table->dropColumn('state');
//            $table->dropColumn('note');
            $table->dropColumn('batch');
            $table->unsignedTinyInteger('pending')->default(0)->after('total_price');
            $table->unsignedTinyInteger('accepted')->default(0)->after('total_price');
            $table->unsignedTinyInteger('doing')->default(0)->after('total_price');
            $table->unsignedTinyInteger('done')->default(0)->after('total_price');
            $table->unsignedTinyInteger('delivering')->default(0)->after('total_price');
            $table->unsignedTinyInteger('completed')->default(0)->after('total_price');
            $table->unsignedTinyInteger('canceled')->default(0)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_batchs', function (Blueprint $table) {
            $table->dropColumn(['is_canceled', 'state', 'reason', 'note']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('state')->default(0)->after('quantity');
            $table->integer('batch')->nullable()->after('quantity');
            $table->boolean('is_served')->default(0)->after('quantity');
            $table->boolean('is_done')->default(0)->after('quantity');
            $table->boolean('is_canceled')->default(0)->after('quantity');
            $table->string('reason')->default('')->after('quantity');
//            $table->string('note')->nullable()->after('is_done');
            $table->dropColumn(['pending', 'accepted', 'doing', 'done', 'delivering', 'completed', 'canceled']);
        });
    }
}
