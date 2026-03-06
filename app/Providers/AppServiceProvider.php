<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // حل مشكلة طول مفتاح قاعدة البيانات في بعض السيرفرات
        Schema::defaultStringLength(191);

        // إنشاء مجلدات التخزين في /tmp إذا لم تكن موجودة (خاص بـ Vercel)
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