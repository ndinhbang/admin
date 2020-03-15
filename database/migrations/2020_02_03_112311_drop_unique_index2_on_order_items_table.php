<?php

use App\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndex2OnOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Throwable
     */
    public function up()
    {
        $items = OrderItem::withoutGlobalScopes()->where('uuid', '=', '')->get();
        if (!$items->isEmpty()) {
            DB::transaction(
                function () use ( $items ) {
                    foreach ( $items as $item ) {
                        $item->uuid = nanoId();
                        $item->save();
                    }
                }, 5
            );
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropUnique('order_items_order_id_product_id_parent_id_unique');
            $table->unique('uuid');
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
