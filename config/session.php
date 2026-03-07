<?php

use Illuminate\Support\Str;

return [

    // 1. تغيير الدريفير إلى cookie لضمان العمل على Vercel بدون مشاكل صلاحيات
    'driver' => env('SESSION_DRIVER', 'cookie'),

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    'encrypt' => env('SESSION_ENCRYPT', false),

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION'),

    'table' => env('SESSION_TABLE', 'sessions'),

    'store' => env('SESSION_STORE'),

    'lottery' => [2, 100],

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    'path' => env('SESSION_PATH', '/'),

    'domain' => env('SESSION_DOMAIN'),

    // 2. إجبار الكوكي على أن تكون Secure لأننا نستخدم HTTPS
    'secure' => env('SESSION_SECURE_COOKIE', true),

    'http_only' => env('SESSION_HTTP_ONLY', true),

    // 3. تغيير Same Site إلى lax أو none للسماح بالـ Redirect الآمن
    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];