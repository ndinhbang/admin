<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceByTimeColumnToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('price_by_time')->default(0)->after('opened');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dateTime('time_out')->nullable()->after('quantity'); // Giờ vào
            $table->dateTime('time_in')->nullable()->after('quantity'); // Giờ ra
            $table->integer('time_used')->default(0)->after('quantity'); // Phút sử dụng
            $table->boolean('price_by_time')->default(0)->after('quantity'); // Tính giờ
            $table->double('product_price', 12, 2)->default(0)->before('total_price'); // Tính giờ
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_by_time']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['time_out', 'time_in', 'time_used', 'price_by_time', 'product_price']);
        });
    }
}
