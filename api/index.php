<?php

// 1. تحميل المحرك (Autoloader) - هذا السطر هو الأهم ولولاه يظهر خطأ Class Not Found
require __DIR__ . '/../vendor/autoload.php';

// 2. توجيه الكاش إلى المجلد المؤقت
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp/views');

// 3. تجهيز المجلدات المؤقتة
if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0777, true);
}

if (!file_exists('/tmp/database.sqlite')) {
    if (!is_dir('/tmp')) {
        mkdir('/tmp', 0777, true);
    }
    touch('/tmp/database.sqlite');
}

// 4. تحميل تطبيق Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 5. ربط مسار الـ Public (لحل مشكلة التصميم)
$app->bind('path.public', function() {
    return __DIR__ . '/../public';
});

// 6. تشغيل أوامر قاعدة البيانات
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true]);
} catch (\Exception $e) {
    error_log("Database Error: " . $e->getMessage());
}

// 7. تشغيل الموقع
require __DIR__ . '/../public/index.php';