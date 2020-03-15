<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Place;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $order;
    private $place;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\Place  $place
     */
    public function __construct(Order $order, Place $place)
    {
        $this->order = $order;
        $this->place = $place;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('place.' . $this->place->uuid);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'order.paid';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     * @throws \Exception
     */
    public function broadcastWith()
    {
        return [ 'order_uuid' => $this->order->uuid ];
    }
}
