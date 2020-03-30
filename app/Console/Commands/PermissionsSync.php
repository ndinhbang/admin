<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Role;
use App\Models\Permission;

class PermissionsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ lại cấu hình Permission trong default.php';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $baseRoles = config('default.roles.root');
        $basePermissions = config('default.permissions');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        \DB::transaction(function () use ($baseRoles, $basePermissions) {

            $permissions = Permission::all();
            $permissionsArr = $permissions->pluck('name')->toArray();

            foreach ($basePermissions as $bkey => $bp) {
                if(in_array($bkey, $permissionsArr)) {
                    Permission::where('name', $bp['name'])->update(['title' => $bp['title']]);
                } else {
                    $permission = Permission::create([
                        'uuid' => nanoId(),
                        'name' => $bp['name'],
                        'title' => $bp['title'],
                    ]);

                    $this->info($bp['name'].': Created permission!');

                    foreach ($bp['roles'] as $roleName) {
                        if(isset($baseRoles[$roleName])) {
                            $permission->assignRole([$roleName]);
                            $this->info($bp['name'].' assinged to -> '.$roleName);
                        }
                    }

                }
            }
        }, 5);
    }
}
