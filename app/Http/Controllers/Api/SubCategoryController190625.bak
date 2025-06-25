<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    public function index()
    {
        return response()->json(Subcategory::all());
    }

    public function show($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        return response()->json($subcategory);
    }
    public function store(Request $request)
    {
        $request->validate([
            'subcategoryname' => 'required|string|max:255',
        ]);

        $subcategory = Subcategory::create([
            'subcategoryname' => $request->subcategoryname,
        ]);

        return response()->json($subcategory, 201);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'subcategoryname' => 'required|string|max:255',
        ]);

        $subcategory->update([
            'subcategoryname' => $request->subcategoryname,
        ]);

        return response()->json($subcategory);
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully.']);
    }
}
