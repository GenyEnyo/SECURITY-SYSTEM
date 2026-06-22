<?php

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
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\SecurityCompanyController;
use App\Http\Controllers\SeverityController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::view('/login', 'auth.login')->name('login');
Route::post('/logout', fn () => redirect()->route('login'))->name('logout');

Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::prefix('kpi')->name('kpi.')->group(function () {
    Route::get('settings',                     [KpiGroupController::class, 'index'])  ->name('settings');
    Route::post('groups',                      [KpiGroupController::class, 'store'])  ->name('groups.store');
    Route::put('groups/{kpiGroup}',            [KpiGroupController::class, 'update']) ->name('groups.update');
    Route::delete('groups/{kpiGroup}',         [KpiGroupController::class, 'destroy'])->name('groups.destroy');

    Route::post('groups/{kpiGroup}/sub-items', [KpiSubItemController::class, 'store'])  ->name('sub-items.store');
    Route::put('sub-items/{kpiSubItem}',       [KpiSubItemController::class, 'update']) ->name('sub-items.update');
    Route::delete('sub-items/{kpiSubItem}',    [KpiSubItemController::class, 'destroy'])->name('sub-items.destroy');

    Route::get('reports',         [KpiReportController::class, 'index'])    ->name('reports.index');
    Route::get('reports/monthly', [KpiReportController::class, 'monthly'])  ->name('reports.monthly');
    Route::get('compliance',      [KpiComplianceController::class, 'index'])->name('compliance');
});
Route::resource('kpi/entries', KpiEntryController::class);
Route::resource('kpi/records', KpiRecordController::class)
    ->only(['index', 'show', 'edit', 'update', 'destroy']);

Route::get('/incidents/all', [IncidentOccurrenceController::class, 'all'])->name('incidents.all');
Route::post('incidents/{incident}/acknowledge', [IncidentOccurrenceController::class, 'acknowledge'])
    ->name('incidents.acknowledge');
Route::resource('incidents', IncidentOccurrenceController::class)->parameters(['incidents' => 'incident']);

Route::view('/my-submissions', 'my-submissions')->name('submissions.index');

Route::get('locations', [BuildingController::class, 'index'])->name('locations.index');
Route::resource('locations', LocationController::class)
    ->only(['store', 'update', 'destroy']);
Route::resource('buildings', BuildingController::class)
    ->only(['store', 'update', 'destroy']);
Route::resource('buildings.places', PlaceController::class)
    ->only(['index', 'store', 'update', 'destroy']);
Route::put('buildings/{building}/place-estimates', [PlaceController::class, 'updateEstimates'])
    ->name('buildings.place-estimates.update');

Route::get('deployments', [DeploymentController::class, 'picker'])->name('deployments.picker');
Route::resource('buildings.deployments', DeploymentController::class);
Route::resource('security-companies', SecurityCompanyController::class)
    ->parameters(['security-companies' => 'securityCompany']);

Route::view('/users', 'users')->name('users.index');

Route::prefix('settings')->name('settings.')->group(function () {
    Route::resource('incident-types', IncidentTypeController::class)
        ->parameters(['incident-types' => 'incidentType'])
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('severity-levels', SeverityController::class)
        ->parameters(['severity-levels' => 'severity'])
        ->only(['index', 'store', 'update', 'destroy']);
});
