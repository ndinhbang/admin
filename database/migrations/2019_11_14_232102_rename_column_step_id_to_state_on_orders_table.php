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
            $table->renameColumn('step_id', 'state');
            // 0 - pending
            // 10 - accepted: khi item đầu tiên chuyển sang trangj thái accepted
            // 20 - processing: khi item đầu tiên chuyển sang trạng thái processing
            // 30 - done: tất cả các món đã làm xong
            // 40 - delivering:  đang giao khách
            // 50 - delivered: tất cả các món đã được giao cho khách
            // 100 - served: order đã được  phục vụ xong khi tất cả các items đã được served
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
