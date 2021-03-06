<?php

use App\Doctrine\TinyInteger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnStepIdToStateOnOrderItemsTable extends Migration
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

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('step_id', 'state');
            // 0 - pending
            // 1 - accepted: chấp nhận / báo bếp
            // 2 - processing: đang làm
            // 3 - done: đã làm xong
            // 4 - delivering:  đang giao khách
            // 5 - served: đã giao khách
            // 6 - completed: đã hoàn thành
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedTinyInteger( 'state')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('state', 'step_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger( 'step_id')->default(0)->change();
        });
    }
}
