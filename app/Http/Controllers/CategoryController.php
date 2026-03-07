<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return Inertia::render('admin/categories/index', [
            "categories" => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404, 'Not Found');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. تحقق أساسي من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 2. فحص يدوي للتكرار (لتجنب خطأ SQLite في Vercel)
        $exists = Category::where('name', $request->name)->exists();
        
        if ($exists) {
            return back()->withErrors(['name' => 'هذه الفئة موجودة بالفعل']);
        }

        // 3. حفظ الفئة
        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'تمت إضافة الفئة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404, 'Not Found');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404, 'Not Found');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // تم تعديل التحقق هنا أيضاً لتجنب نفس الخطأ
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // فحص يدوي للتكرار مع استثناء الفئة الحالية
        $exists = Category::where('name', $request->name)
                          ->where('id', '!=', $id)
                          ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'هذا الاسم مستخدم في فئة أخرى']);
        }

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->withErrors([
                'error' => 'لا يمكن حذف فئة تحتوي على منتجات. يرجى نقل أو حذف المنتجات أولاً.'
            ]);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'تم حذف الفئة بنجاح');
    }
}