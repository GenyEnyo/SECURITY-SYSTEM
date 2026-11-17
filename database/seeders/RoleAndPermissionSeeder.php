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
            'Manage Incident Statuses',
            'Manage Company Statuses',
            'Manage Staff Locations',

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
            'View All Locations',
        ];

        $all = array_merge($operational, $admin);

        foreach ($all as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Only Super-Admin may manage permissions themselves.
        $allButPermissions = array_values(array_diff($all, [
            'View Permission',
            'Create Permission',
            'Edit Permission',
        ]));

        $roles = [
            'Super-Admin'         => $all,
            'Admin'               => $allButPermissions,
            // Head of Security and all admin staff.
            'Head of Security'    => $allButPermissions,
            // Views reports for their own unit (location).
            'Location Supervisor' => [
                'View Dashboard',
                'View Incidents',
                'View All Incidents',
                'View KPI Records',
                'View KPI Reports',
                'View Deployment Compliance',
                'View Locations',
                'View Deployments',
                'View Security Companies',
            ],
            // All security staff and guards.
            'Security Officer'    => [
                'View Dashboard',
                'Report Incident',
                'View Incidents',
                'Edit Incident',
                'Submit KPI Scorecard',
                'View Locations',
                'View Deployments',
                'Manage Deployments',
                'View Security Companies',
                'View My Submissions',
            ],
            // Minimal: create-only data entry.
            'Data Entry Officer'  => [
                'View Dashboard',
                'Report Incident',
                'View Incidents',
                'Submit KPI Scorecard',
                'View Deployments',
                'Manage Deployments',
                'View My Submissions',
            ],
        ];

        foreach ($roles as $name => $permissions) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
                ->syncPermissions($permissions);
        }
    }
}
