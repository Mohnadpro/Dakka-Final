<?php

// 1. تحميل المحرك (Autoloader)
require __DIR__ . '/../vendor/autoload.php';

// 2. توجيه الكاش إلى المجلد المؤقت
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp/views');

if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0777, true);
}

// 3. تحميل تطبيق Laravel
$app = require __DIR__ . '/../bootstrap/app.php';

// 4. ربط مسار الـ Public وتحديد المانيفست
$app->bind('path.public', function() {
    return __DIR__ . '/../public';
});
$app->usePublicPath(__DIR__ . '/../public');
putenv('VITE_MANIFEST_PATH=' . __DIR__ . '/../public/build/manifest.json');

// 5. تشغيل الموقع (بدون محاولة عمل migrate هنا لضمان السرعة)
$app->handleRequest(Illuminate\Http\Request::capture());