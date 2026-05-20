<?php

use App\Http\Controllers\IncidentOccurrenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::view('/login', 'auth.login')->name('login');
Route::post('/logout', fn () => redirect()->route('login'))->name('logout');

Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::view('/kpi/settings', 'kpi-settings')->name('kpi.settings');
Route::view('/kpi/entry', 'kpi-entry')->name('kpi.entry');

Route::get('/incidents/all', [IncidentOccurrenceController::class, 'all'])->name('incidents.all');
Route::resource('incidents', IncidentOccurrenceController::class)->parameters(['incidents' => 'incident']);

Route::view('/my-submissions', 'my-submissions')->name('submissions.index');
Route::view('/locations', 'locations')->name('locations.index');
Route::view('/users', 'users')->name('users.index');
