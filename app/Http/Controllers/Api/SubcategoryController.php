<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    public function index()
    {
        return response()->json(Subcategory::with('category')->get());
    }

    public function show($id)
    {
        return response()->json(Subcategory::with('category')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subcategoryname' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,categoryid'
        ]);

        $subcategory = Subcategory::create($request->only(['subcategoryname', 'category_id']));

        return response()->json($subcategory, 201);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'subcategoryname' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,categoryid'
        ]);

        $subcategory->update($request->only(['subcategoryname', 'category_id']));

        return response()->json($subcategory);
    }

    public function destroy($id)
    {
        Subcategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Subcategory deleted successfully.']);
    }
}
