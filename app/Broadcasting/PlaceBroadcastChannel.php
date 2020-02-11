<?php

namespace App\Broadcasting;

use App\Models\Place;
use App\User;
use Illuminate\Support\Facades\DB;

class PlaceBroadcastChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User          $user
     * @param  \App\Models\Place  $place
     * @return bool
     */
    public function join(User $user, Place $place)
    {
        return DB::table('place_user')
            ->where('place_id', $place->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
