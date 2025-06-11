<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('subcategory')->get());
    }

    public function show($id)
    {
        $category = Category::with('subcategory')->findOrFail($id);
        return response()->json($category);
    }
}
