<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('promotion_applieds');
        Schema::dropIfExists('promotions_customers');
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid',21)->unique();
            $table->unsignedInteger('place_id')->nullable()->index();
            $table->string('name');
            $table->string('code');
            $table->string('type', 10); // product or order
            $table->unsignedTinyInteger('state')->default(0); //0: lưu tạm / 1: hoạt động /2: dừng
            $table->dateTime('from');
            $table->dateTime('to')->nullable();
            $table->boolean('is_limited')->default(0); // có giới hạn số lượng km ?
            $table->unsignedInteger('limit_qty')->default(0); // số lượng km
            $table->unsignedInteger('remain_qty')->default(0); // số lượng còn lại
            $table->boolean('applied_all_customers')->default(1); // áp dụng cho tất cả khách hàng ?
            $table->boolean('applied_all_products')->default(1); // áp dụng cho tất cả mặt hàng ?
            $table->boolean('required_code')->default(0); // yêu cầu nhập mã khi áp dụng ?
            $table->json('total'); // tổng km, tổng giá
            $table->json('rules');
            $table->json('customers');
            $table->json('segments');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
