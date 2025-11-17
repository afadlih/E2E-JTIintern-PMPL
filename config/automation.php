<?php

return [
    'intervals' => [
        'local' => 120,        // 2 menit untuk debug
        'testing' => 300,      // 5 menit untuk testing
        'staging' => 1800,     // 30 menit untuk staging
        'production' => 3600,  // 1 jam untuk production
    ],
    
    'batch_sizes' => [
        'local' => 3,
        'testing' => 5,
        'staging' => 10,
        'production' => 20,
    ],
    
    'cache_durations' => [
        'local' => 240,        // 4 menit
        'testing' => 600,      // 10 menit
        'staging' => 1800,     // 30 menit
        'production' => 3600,  // 1 jam
    ],
    
    'business_hours' => [
        'enabled' => env('AUTOMATION_BUSINESS_HOURS', true),
        'start' => 8,
        'end' => 17,
        'timezone' => 'Asia/Jakarta'
    ],
    
    'rate_limits' => [
        'local' => 5,          // 5 menit
        'testing' => 10,       // 10 menit
        'staging' => 20,       // 20 menit
        'production' => 30,    // 30 menit
    ]
];
