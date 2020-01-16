<?php

namespace App\Events;

use App\Http\Resources\PosOrderResource;
use App\Models\Order;
use App\Models\Place;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//use Illuminate\Broadcasting\Channel;
//use Illuminate\Broadcasting\PresenceChannel;
class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
//    private $place;
//    private $usingArr;
    /**
     * Create a new event instance.
     *
     * @param  array  $order
     */
    public function __construct($order)
    {
        $this->order    = $order;
//        $this->place    = $place;
//        $this->usingArr = $usingArr;
        // load missing relations
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('place.' . $this->order->place_uuid);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'order.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     * @throws \Exception
     */
//    public function broadcastWith()
//    {
//        return [
//            'order' => $this->order
//        ];
//    }
}
