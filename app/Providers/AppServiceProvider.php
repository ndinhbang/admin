<?php

namespace App\Providers;

use Bouncer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
//        Bouncer::ownedVia('App\Tenant', 'owner_id');
        // Bouncer custom models
        Bouncer::useAbilityModel(\App\Models\Ability::class);
        Bouncer::useRoleModel(\App\Models\Role::class);
    }
}
