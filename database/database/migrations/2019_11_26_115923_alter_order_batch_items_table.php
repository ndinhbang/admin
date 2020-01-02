<?php

use App\Doctrine\TinyInteger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderBatchItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up()
    {
        // @link: https://github.com/laravel/framework/issues/8840#issuecomment-503824498
        Schema::registerCustomDoctrineType(TinyInteger::class, TinyInteger::NAME, 'TINYINT');

        Schema::table('order_batch_items', function (Blueprint $table) {
            $table->dropColumn(['place_id', 'order_id', 'product_id']);
            $table->tinyInteger('state')->default(0)->after('quantity')
            ->comment('0: pending / 1: doing / 2: done / -1: out of stock')->change();
            $table->unsignedInteger('item_id')->after('id');
            $table->unsignedInteger('batch_id')->after('id');

            $table->unique(['item_id', 'batch_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_batch_items', function (Blueprint $table) {
            $table->integer('place_id')->after('id');
            $table->integer('order_id')->after('id');
            $table->integer('product_id')->after('id');
            $table->dropColumn(['item_id', 'batch_id']);
        });
    }
}
