<?php

use Illuminate\Database\Seeder;

class BouncerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            // Create default roles
            $roles = config('default.roles');
            foreach ($roles as $role) {
                Bouncer::role()->firstOrCreate(Arr::only($role, ['name', 'title', 'level']));
            }

            // Create default abilities
            $abilities = config('default.permissions.root');
            foreach ($abilities as $ability) {
//                foreach ($grouped as $ability) {
                    Bouncer::ability()->firstOrCreate(Arr::only($ability, ['name', 'title']));
                    $rolesHaveAbility = Arr::only($ability, ['roles']);
                    // give role its abilitiy
                    foreach ($rolesHaveAbility as $role) {
                        Bouncer::allow($role)->to($ability['name']);
                    }
//                }
            }

            // superadmin role can do everything
            Bouncer::allow('superadmin')->everything();
        });
    }
}
