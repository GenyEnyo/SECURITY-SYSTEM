<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\IncidentOccurrenceController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\KpiEntryController;
use App\Http\Controllers\KpiComplianceController;
use App\Http\Controllers\KpiGroupController;
use App\Http\Controllers\KpiRecordController;
use App\Http\Controllers\KpiReportController;
use App\Http\Controllers\KpiSubItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SecurityCompanyController;
use App\Http\Controllers\SeverityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/access-pending', [AuthController::class, 'accessPending'])->name('access.pending');

    Route::view('/dashboard', 'dashboard')->name('dashboard')->middleware('permission:View Dashboard');

    Route::prefix('kpi')->name('kpi.')->group(function () {
        Route::middleware('permission:Manage KPI Settings')->group(function () {
            Route::get('settings',                     [KpiGroupController::class, 'index'])  ->name('settings');
            Route::post('groups',                      [KpiGroupController::class, 'store'])  ->name('groups.store');
            Route::put('groups/{kpiGroup}',            [KpiGroupController::class, 'update']) ->name('groups.update');
            Route::delete('groups/{kpiGroup}',         [KpiGroupController::class, 'destroy'])->name('groups.destroy');

            Route::post('groups/{kpiGroup}/sub-items', [KpiSubItemController::class, 'store'])  ->name('sub-items.store');
            Route::put('sub-items/{kpiSubItem}',       [KpiSubItemController::class, 'update']) ->name('sub-items.update');
            Route::delete('sub-items/{kpiSubItem}',    [KpiSubItemController::class, 'destroy'])->name('sub-items.destroy');
        });

        Route::middleware('permission:View KPI Reports')->group(function () {
            Route::get('reports',         [KpiReportController::class, 'index'])  ->name('reports.index');
            Route::get('reports/monthly', [KpiReportController::class, 'monthly'])->name('reports.monthly');
        });
        Route::get('compliance', [KpiComplianceController::class, 'index'])->name('compliance')
            ->middleware('permission:View Deployment Compliance');
    });
    Route::resource('kpi/entries', KpiEntryController::class)
        ->middleware('permission:Submit KPI Scorecard');
    Route::resource('kpi/records', KpiRecordController::class)
        ->only(['index', 'show'])
        ->middleware('permission:View KPI Records');
    Route::resource('kpi/records', KpiRecordController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:Edit KPI Record');
    Route::resource('kpi/records', KpiRecordController::class)
        ->only(['destroy'])
        ->middleware('permission:Delete KPI Record');

    // Per-action permissions for incidents are declared on the controller.
    Route::get('/incidents/all', [IncidentOccurrenceController::class, 'all'])->name('incidents.all');
    Route::post('incidents/{incident}/acknowledge', [IncidentOccurrenceController::class, 'acknowledge'])
        ->name('incidents.acknowledge');
    Route::resource('incidents', IncidentOccurrenceController::class)->parameters(['incidents' => 'incident']);

    Route::view('/my-submissions', 'my-submissions')->name('submissions.index')
        ->middleware('permission:View My Submissions');

    Route::middleware('permission:View Locations')->group(function () {
        Route::get('locations', [BuildingController::class, 'index'])->name('locations.index');
        Route::resource('buildings.places', PlaceController::class)
            ->only(['index']);
    });
    Route::middleware('permission:Manage Locations')->group(function () {
        Route::resource('locations', LocationController::class)
            ->only(['store', 'update', 'destroy']);
        Route::resource('buildings', BuildingController::class)
            ->only(['store', 'update', 'destroy']);
        Route::resource('buildings.places', PlaceController::class)
            ->only(['store', 'update', 'destroy']);
        Route::put('buildings/{building}/place-estimates', [PlaceController::class, 'updateEstimates'])
            ->name('buildings.place-estimates.update');
    });

    // The Manage group comes first so /buildings/{building}/deployments/create
    // is not captured by the show route.
    Route::resource('buildings.deployments', DeploymentController::class)
        ->except(['index', 'show'])
        ->middleware('permission:Manage Deployments');
    Route::middleware('permission:View Deployments')->group(function () {
        Route::get('deployments', [DeploymentController::class, 'picker'])->name('deployments.picker');
        Route::resource('buildings.deployments', DeploymentController::class)
            ->only(['index', 'show']);
    });

    Route::resource('security-companies', SecurityCompanyController::class)
        ->parameters(['security-companies' => 'securityCompany'])
        ->except(['index', 'show'])
        ->middleware('permission:Manage Security Companies');
    Route::resource('security-companies', SecurityCompanyController::class)
        ->parameters(['security-companies' => 'securityCompany'])
        ->only(['index', 'show'])
        ->middleware('permission:View Security Companies');

    Route::get('users', [UserController::class, 'index'])->name('users.index')
        ->middleware('permission:View Users');
    Route::middleware('permission:Assign Roles')->group(function () {
        Route::get('users/{user}/roles', [UserController::class, 'addRoleToUser'])->name('users.roles.edit');
        Route::put('users/{user}/roles', [UserController::class, 'giveRoleToUser'])->name('users.roles.update');
    });

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')
        ->middleware('permission:View Role');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')
        ->middleware('permission:Create Role');
    Route::middleware('permission:Edit Role')->group(function () {
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::get('roles/{role}/permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.permissions.edit');
        Route::put('roles/{role}/permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.permissions.update');
    });

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')
        ->middleware('permission:View Permission');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store')
        ->middleware('permission:Create Permission');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')
        ->middleware('permission:Edit Permission');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index')
        ->middleware('permission:View Audit Logs');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('incident-types', IncidentTypeController::class)
            ->parameters(['incident-types' => 'incidentType'])
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('permission:Manage Incident Types');
        Route::resource('severity-levels', SeverityController::class)
            ->parameters(['severity-levels' => 'severity'])
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('permission:Manage Severity Levels');
    });
});
