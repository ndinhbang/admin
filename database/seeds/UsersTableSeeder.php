<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create first user and assign superadmin role
        if (App\User::all()->isEmpty()) {
            // create a user
            $user = factory(App\User::class)->create();
            // assign superadmin role to user
            Bouncer::assign('superadmin')->to($user);
        }

    }
}
