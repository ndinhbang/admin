<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_steps', function (Blueprint $table) {
            if (Schema::hasColumn('order_steps', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
            if (Schema::hasColumn('order_steps', 'is_finsihed')) {
                $table->renameColumn('is_finsihed', 'is_served');
            }
            if (!Schema::hasColumn('order_steps', 'next_id')) {
                $table->integer('next_id')->unsigned()->index();
            }
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('is_delivered', 'is_served');
            $table->boolean('is_canceled')->default(0)->after('step_id');
            $table->string('reason')->default(0)->after('is_canceled'); // ly do huy
            $table->string('note')->default(0)->after('reason'); // ghi chu
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('is_finished', 'is_served');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_steps', function (Blueprint $table) {
            $table->boolean('is_paid')->default(0)->after('is_delivered');
            $table->renameColumn('is_served', 'is_finsihed');
            $table->dropColumn('next_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('is_served', 'is_delivered');
            $table->dropColumn(['is_canceled', 'reason', 'note']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('is_served', 'is_finished');
        });

    }
}
