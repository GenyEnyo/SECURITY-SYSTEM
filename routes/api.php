<?php

use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Read-only analytics consumed by the management (mgt) system.
Route::prefix('v1/analytics')->middleware('api.token')->group(function () {
    Route::get('summary',     [AnalyticsController::class, 'summary']);
    Route::get('incidents',   [AnalyticsController::class, 'incidents']);
    Route::get('deployment',  [AnalyticsController::class, 'deployment']);
    Route::get('kpi',         [AnalyticsController::class, 'kpi']);
    Route::get('contractors', [AnalyticsController::class, 'contractors']);
    Route::get('dashboard',   [AnalyticsController::class, 'dashboard']);
});
