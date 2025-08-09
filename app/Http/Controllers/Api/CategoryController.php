<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('subcategories')->get());
    }

    public function show($id)
    {
        return response()->json(Category::with('subcategories')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoryname' => 'required|string|max:255|unique:categories,categoryname'
        ]);

        $category = Category::create($request->only('categoryname'));

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'categoryname' => 'required|string|max:255|unique:categories,categoryname,' . $category->categoryid . ',categoryid'
        ]);

        $category->update($request->only('categoryname'));

        return response()->json($category);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
