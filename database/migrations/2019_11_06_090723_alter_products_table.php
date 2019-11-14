<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {            
            $table->string('code',25)->after('uuid');
            $table->boolean('can_stock')->default(1)->after('state'); // co quan ly ton kho ?
            $table->boolean('opened')->default(0)->after('state'); // mo ban ?
            $table->string('type',25)->default('simple')->after('state'); // simple
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
