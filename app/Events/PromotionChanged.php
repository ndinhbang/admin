<?php

namespace App\Events;

use App\Models\Promotion;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromotionChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $promotion;

    protected $place_uuid;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Promotion  $promotion
     * @param                         $place_uuid
     */
    public function __construct(Promotion $promotion, $place_uuid)
    {
        $this->promotion  = $promotion;
        $this->place_uuid = $place_uuid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('place.' . $this->place_uuid);
    }

    public function broadcastAs()
    {
        return 'promotion.changed';
    }
}
