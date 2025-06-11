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
}
