<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('promotion_applied')->nullable();
            $table->unsignedInteger('promotion_id')->nullable()->index()->after('amount');
            $table->char('promotion_uuid', 21)->nullable()->after('amount');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('discount_id');
            $table->unsignedInteger('promotion_id')->nullable()->index()->after('note');
            $table->char('promotion_uuid', 21)->nullable()->after('note');
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
            $table->dropColumn(['promotion_id', 'promotion_uuid', 'promotion_applied']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['promotion_id', 'promotion_uuid']);
            $table->unsignedInteger('discount_id')->nullable()->index()->after('note');
        });
    }
}
