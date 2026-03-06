<?php
// 1. تجهيز قاعدة البيانات في مجلد مؤقت يسمح به Vercel
if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

// 2. تشغيل ملف Laravel الأساسي
require __DIR__ . '/../public/index.php';

// 3. أمر سحري لإنشاء الجداول تلقائياً عند أول تشغيل
try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true]);
} catch (\Exception $e) {
    // إذا كانت الجداول موجودة بالفعل، سيتجاهل الأمر ولن يحدث خطأ
}