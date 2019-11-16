<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Doctrine\TinyInteger;

class RenameColumnStepIdToStateOnOrdersTable extends Migration
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

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('step_id', 'state')->comment('0: pending / 1: accepted / 2: processing / 3: done / 4: delivering / 5: served / 6: completed');
            // 0 - pending
            // 1 - accepted: khi item đầu tiên chuyển sang trangj thái accepted
            // 2 - processing: khi item đầu tiên chuyển sang trạng thái processing
            // 3 - done: tất cả các món đã làm xong
            // 4 - delivering:  đang giao khách
            // 5 - served: tất cả các món đã được giao cho khách
            // 6 - completed: order đã được  phục vụ xong khi tất cả các items đã được served
        });

        Schema::table('orders', function (Blueprint $table) {
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
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('state', 'step_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger( 'step_id')->default(0)->change();
        });
    }
}
