<?php

// 1. إخبار Laravel بتوجيه كل ملفات "الكاش" إلى المجلد المؤقت المسموح به في Vercel
// هذا السطر يحل مشكلة الخطأ 500 و "directory must be writable" نهائياً
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp/views');

// 2. تجهيز قاعدة البيانات في مجلد مؤقت يسمح به Vercel
if (!file_exists('/tmp/database.sqlite')) {
    if (!is_dir('/tmp')) {
        mkdir('/tmp', 0777, true);
        if (!is_dir('/tmp/views')) { mkdir('/tmp/views', 0777, true); }
    }
    touch('/tmp/database.sqlite');
}

// 3. تشغيل ملف Laravel الأساسي (تأكد من المسار الصحيح)
require __DIR__ . '/../public/index.php';

// 4. أمر سحري لإنشاء الجداول تلقائياً (Migration)
try {
    // نستخدم الـ Application الفعلي الذي تم إنشاؤه في الخطوة رقم 3
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true]);
} catch (\Exception $e) {
    // تجاهل الأخطاء إذا كانت الجداول موجودة مسبقاً
}