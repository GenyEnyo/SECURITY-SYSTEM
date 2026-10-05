<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $operational = [
            'View Dashboard',

            'Report Incident',
            'View Incidents',
            'View All Incidents',
            'Edit Incident',
            'Delete Incident',
            'Acknowledge Incident',

            'Submit KPI Scorecard',
            'View KPI Records',
            'Edit KPI Record',
            'Delete KPI Record',
            'View KPI Reports',
            'View Deployment Compliance',
            'Manage KPI Settings',

            'View Locations',
            'Manage Locations',
            'View Deployments',
            'Manage Deployments',
            'View Security Companies',
            'Manage Security Companies',

            'Manage Incident Types',
            'Manage Severity Levels',

            'View My Submissions',
        ];

        $admin = [
            'View Users',
            'Assign Roles',
            'View Role',
            'Create Role',
            'Edit Role',
            'View Permission',
            'Create Permission',
            'Edit Permission',
            'View Audit Logs',
        ];

        $all = array_merge($operational, $admin);

        foreach ($all as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'Super-Admin'      => $all,
            'Admin'            => $all,
            'Head of Security' => $operational,
            'Supervisor'       => [
                'View Dashboard',
                'Report Incident',
                'View Incidents',
                'View All Incidents',
                'Edit Incident',
                'Acknowledge Incident',
                'Submit KPI Scorecard',
                'View KPI Records',
                'Edit KPI Record',
                'View KPI Reports',
                'View Deployment Compliance',
                'View Locations',
                'View Deployments',
                'Manage Deployments',
                'View Security Companies',
                'View My Submissions',
            ],
            'Senior Officer'   => [
                'View Dashboard',
                'Report Incident',
                'View Incidents',
                'Edit Incident',
                'Submit KPI Scorecard',
                'View My Submissions',
            ],
        ];

        foreach ($roles as $name => $permissions) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
                ->syncPermissions($permissions);
        }
    }
}
