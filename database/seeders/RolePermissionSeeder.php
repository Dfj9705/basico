<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /**
         * Recursos del sistema
         */
        $resources = [
            'users',
            'roles',
            'permissions',

            'grades',
            'weapon_branches',

            'meal_attendances',
        ];

        /**
         * Acciones permitidas
         */
        $actions = [
            'view',
            'create',
            'update',
            'delete',
        ];

        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permission = "{$action}_{$resource}";

                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);

                $permissions[] = $permission;
            }
        }

        /**
         * Roles
         */
        $admin = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web',
        ]);

        $student = Role::firstOrCreate([
            'name' => 'Alumno',
            'guard_name' => 'web',
        ]);

        /**
         * El administrador tiene todos los permisos.
         */
        $admin->syncPermissions($permissions);

        /**
         * El alumno inicia sin permisos administrativos.
         * Los permisos se asignarán según sea necesario.
         */
        $student->syncPermissions([]);
    }
}
