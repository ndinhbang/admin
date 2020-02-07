<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_takes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 21)->unique();
            $table->char('code', 20);
            $table->integer('place_id')->unsigned()->index();
            $table->integer('creator_id');
            $table->integer('user_id');

            $table->integer('qty');
            $table->integer('qty_diff');
            $table->integer('qty_excessing');
            $table->integer('qty_missing');

            $table->dateTime('on_date');
            $table->string('note', 500)->nullable(); // ghi chu
            $table->tinyInteger('status')->default(0); // 0: đang kiểm kê | 1: đã kiểm kê xong

            $table->timestamps();
            $table->unique(['place_id', 'code']);
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->renameColumn('quantity', 'qty_import'); // nhập
            // $table->double('quantity', 6, 2)->default(0)->change();

            $table->renameColumn('remain', 'qty_remain'); // tồn
            // $table->double('remain', 6, 2)->default(0)->change();

            $table->double('qty_export', 6, 2)->default(0)->after('quantity'); // xuất

            $table->string('note', 191)->nullable()->after('remain'); // ghi chu
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_takes');

        Schema::table('inventory', function (Blueprint $table) {
            $table->renameColumn('qty_import', 'quantity'); // nhập
            $table->renameColumn('qty_remain', 'remain'); // tồn
            $table->dropColumn('note');
            $table->dropColumn('qty_export');
        });
    }
}
