<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics API token
    |--------------------------------------------------------------------------
    | Shared secret the management (mgt) system sends as a Bearer token when
    | calling /api/v1/analytics/*. Set the same value in mgt's .env.
    */
    'api_token' => env('MGT_API_TOKEN'),

    /*
    | Acknowledgement SLA targets, in hours, keyed by severity name (case-insensitive).
    */
    'ack_sla_hours' => [
        'urgent' => 1,
        'high'   => 4,
        'medium' => 24,
        'low'    => 72,
    ],

    /*
    | Severity weights used for the site risk score.
    */
    'severity_weights' => [
        'low'    => 1,
        'medium' => 2,
        'high'   => 3,
        'urgent' => 5,
    ],

    // Severities counted as "critical" in trends and the recent-incidents list.
    'critical_severities' => ['high', 'urgent'],

    // A beat/building with at least this many incidents in the rolling window is a hotspot.
    'repeat_threshold'   => 3,
    'repeat_window_days' => 30,

    // A deployment row covers one shift per day; used to turn guard-shifts into guard-hours.
    'shift_hours' => 12,

    // Hours [start, end) that count as the Day shift when classifying incidents.
    'day_shift_start' => 6,
    'day_shift_end'   => 18,
];
