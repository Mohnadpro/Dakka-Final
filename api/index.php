<?php

// 1. توجيه الكاش إلى المجلد المؤقت لتجنب أخطاء نظام الملفات (Read-only)
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp/views');

// 2. تجهيز المجلدات المؤقتة وقاعدة البيانات
if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0777, true);
}

if (!file_exists('/tmp/database.sqlite')) {
    if (!is_dir('/tmp')) {
        mkdir('/tmp', 0777, true);
    }
    touch('/tmp/database.sqlite');
}

// 3. تحميل تطبيق Laravel (نحتاج الـ Autoloader أولاً)
$app = require __DIR__ . '/../bootstrap/app.php';

// 4. الربط الصحيح لمسار الـ Public (لحل مشكلة اختفاء التنسيقات والصور)
$app->bind('path.public', function() {
    return __DIR__ . '/../public';
});

// 5. تشغيل أوامر قاعدة البيانات (Migrations)
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // تشغيل الهجرة لإنشاء الجداول
    $kernel->call('migrate', ['--force' => true]);
    
    // إذا كنت تريد ملء الموقع بمنتجات تجريبية، فك التشفير عن السطر التالي:
    // $kernel->call('db:seed', ['--force' => true]);
    
} catch (\Exception $e) {
    error_log("Database Error: " . $e->getMessage());
}

// 6. تشغيل ملف Laravel الأساسي لمعالجة الطلب
require __DIR__ . '/../public/index.php';