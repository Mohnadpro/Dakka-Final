<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    // نحدد الحقول التي يمكن تعبئتها (الاسم فقط في حالتنا)
    // هذا يحمي قاعدة البيانات ويجعلها تعمل بسلاسة على Vercel
    protected $fillable = ['name'];

    // هذه الدالة هي المسؤولة عن استدعاء المنتجات ولن تتأثر أبداً
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}