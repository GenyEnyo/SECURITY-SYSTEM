<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'log'  => 'nullable|string|max:255',
            'user' => 'nullable|integer',
            'from' => 'nullable|date',
            'to'   => 'nullable|date',
        ]);

        $activities = Activity::with('causer')
            ->when($filters['log'] ?? null, fn ($query, $log) => $query->where('log_name', $log))
            ->when($filters['user'] ?? null, fn ($query, $user) => $query->where('causer_type', User::class)->where('causer_id', $user))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('audit.index', [
            'activities' => $activities,
            'logNames'   => Activity::query()->distinct()->orderBy('log_name')->pluck('log_name'),
            'users'      => User::orderBy('name')->get(['id', 'name']),
            'filters'    => $filters,
        ]);
    }
}
