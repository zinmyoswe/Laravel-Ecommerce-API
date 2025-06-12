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

    public function store(Request $request)
    {
        $request->validate([
            'categoryname' => 'required|string|max:255',
            'subcategoryid' => 'nullable|exists:subcategories,subcategoryid',
        ]);

        $category = Category::create($request->only(['categoryname', 'subcategoryid']));

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'categoryname' => 'required|string|max:255',
            'subcategoryid' => 'nullable|exists:subcategories,subcategoryid',
        ]);

        $category->update($request->only(['categoryname', 'subcategoryid']));

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

}
