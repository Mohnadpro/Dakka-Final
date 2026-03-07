<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL; // أضفنا هذا السطر هنا

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // إخبار Laravel باستخدام مجلد /tmp للكاش في Vercel لتجنب خطأ Read-only
        if (config('app.env') === 'production') {
            $this->app->useStoragePath('/tmp/storage');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. حل مشكلة طول مفتاح قاعدة البيانات
        Schema::defaultStringLength(191);

        // 2. إجبار الروابط على استخدام HTTPS (لحل مشكلة Mixed Content في Vercel)
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // 3. تحديد مسار المانيفست لـ Vite
        Vite::useManifestFilename('.vite/manifest.json');

        // 4. إنشاء مجلدات التخزين المؤقتة في Vercel
        if (config('app.env') === 'production') {
            $storagePath = '/tmp/storage/framework/';
            foreach (['sessions', 'views', 'cache'] as $path) {
                if (!is_dir($storagePath . $path)) {
                    mkdir($storagePath . $path, 0777, true);
                }
            }
        }
    }
}