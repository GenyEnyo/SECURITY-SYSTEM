<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SecurityAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Read-only analytics API for the management (mgt) system.
 *
 * Query params: from, to (Y-m-d, default: start of month → today), location_id (optional).
 */
class AnalyticsController extends Controller
{
    public function __construct(private SecurityAnalyticsService $analytics)
    {
    }

    public function summary(Request $request): JsonResponse
    {
        return $this->respond($request, fn () => $this->analytics->summary());
    }

    public function incidents(Request $request): JsonResponse
    {
        return $this->respond($request, fn () => $this->analytics->incidents());
    }

    public function deployment(Request $request): JsonResponse
    {
        return $this->respond($request, fn () => $this->analytics->deployment());
    }

    public function kpi(Request $request): JsonResponse
    {
        return $this->respond($request, fn () => $this->analytics->kpi());
    }

    public function contractors(Request $request): JsonResponse
    {
        return $this->respond($request, fn () => $this->analytics->contractors());
    }

    public function dashboard(Request $request): JsonResponse
    {
        return $this->respond($request, fn () => $this->analytics->dashboard());
    }

    private function respond(Request $request, callable $build): JsonResponse
    {
        $validated = $request->validate([
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date', 'after_or_equal:from'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
        ]);

        $from       = isset($validated['from']) ? Carbon::parse($validated['from']) : now()->startOfMonth();
        $to         = isset($validated['to']) ? Carbon::parse($validated['to']) : now();
        $locationId = isset($validated['location_id']) ? (int) $validated['location_id'] : null;

        $this->analytics->forPeriod($from, $to, $locationId);

        return response()->json([
            'data' => $build(),
            'meta' => [
                'from'         => $from->toDateString(),
                'to'           => $to->toDateString(),
                'location_id'  => $locationId,
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
