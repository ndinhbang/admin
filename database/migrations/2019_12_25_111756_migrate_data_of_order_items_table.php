<?php

use Illuminate\Database\Migrations\Migration;

class MigrateDataOfOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Throwable
     */
    public function up()
    {
        $places = \App\Models\Place::with([
            'orders'            => function ( $query ) {
                $query->withoutGlobalScopes([ \App\Scopes\PlaceScope::class ]);
            },
            'orders.items' => function ( $query ) {
                $query->withoutGlobalScopes([ \App\Scopes\PlaceScope::class ]);
            },
        ])
            ->get();
        if ( $places->isNotEmpty() ) {
            DB::transaction(
                function () use ( $places ) {
                    foreach ( $places as $place ) {
                        $orders = $place->orders ?? collect([]);
                        if ( $orders->isEmpty() ) {
                            continue;
                        }
                        foreach ( $orders as $order ) {
                            $items = $order->items ?? collect([]);
                            if ( $items->isEmpty() ) {
                                continue;
                            }
                            foreach ( $items as $item ) {
                                $item->update([
                                    'uuid'     => nanoId(),
                                    'place_id' => $place->id,
                                ]);
                            }
                        }
                    }
                }, 5
            );
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        //
    }
}
