<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateLaratrustTables();

        $config = Config::get('laratrust_seeder.roles_structure');
        $roles_translation = Config::get('laratrust_seeder.role_translations');
        $permissions_translation = Config::get('laratrust_seeder.permission_translations');
        $modules_translation = Config::get('laratrust_seeder.modules_translations');

        if ($config === null) {
            $this->command->error("The configuration has not been published. Did you run `php artisan vendor:publish --tag=\"laratrust-seeder\"`");
            $this->command->line('');
            return false;
        }

        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        foreach ($config as $key => $modules) {

            // Create a new role
            $role = \App\Models\Role::create([
                'name' => $key,
                ...$roles_translation[$key],
                'description' => ucwords(str_replace('_', ' ', $key))
            ]);
            $permissions = [];

            $this->command->info('Creating Role ' . strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module => $value) {

                foreach (explode(',', $value) as $perm) {

                    $permissionValue = $mapPermission->get($perm);

                    $permission = \App\Models\Permission::where([
                        'name' => $module . '_' . $permissionValue,
                        'description' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                    ])->first();

                    if (!$permission) {
                        $permissions[] = \App\Models\Permission::create([
                            'name' => $module . '_' . $permissionValue,
                            ...$this->setUpPermissionTranslation($permissions_translation[$perm], $modules_translation[$module]),
                            'description' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                        ])->id;
                    } else {
                        $permissions[] = $permission->id;
                    }
                    $this->command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);
                }
            }

            // Add all permissions to the role
            $role->permissions()->sync($permissions);

            if (Config::get('laratrust_seeder.create_users')) {
                $this->command->info("Creating '{$key}' user");
                // Create default user for each role
                $user = \App\Models\User::create([
                    'name' => ucwords(str_replace('_', ' ', $key)),
                    'email' => $key . '@app.com',
                    'password' => bcrypt('password')
                ]);
                $user->addRole($role);
            }

        }
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return  void
     */
    public function truncateLaratrustTables()
    {
        $this->command->info('Truncating User, Role and Permission tables');
        Schema::disableForeignKeyConstraints();

        DB::table('permission_role')->truncate();
        DB::table('permission_user')->truncate();
        DB::table('role_user')->truncate();

        if (Config::get('laratrust_seeder.truncate_tables')) {
            DB::table('role_translations')->truncate();
            DB::table('roles')->truncate();
            DB::table('permission_translations')->truncate();
            DB::table('permissions')->truncate();

            if (Config::get('laratrust_seeder.create_users')) {
                $usersTable = (new \App\Models\User)->getTable();
                DB::table($usersTable)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    protected function setUpPermissionTranslation($perm, $module): array
    {
        return [
            'ar' => ['display_name' => $perm['ar'] . ' ' . $module['ar']],
            'en' => ['display_name' => $module['en'] . ' ' . $perm['en']],
        ];
    }
}
