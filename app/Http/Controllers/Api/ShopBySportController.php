<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopBySport;
use App\Models\Product;

class ShopBySportController extends Controller
{
    // List all sports
    public function index()
    {
        $sports = ShopBySport::all();
        return response()->json($sports);
    }

    // Show one sport by ID
    // public function show($id)
    // {
    //     $sport = ShopBySport::find($id);
    //     if (!$sport) {
    //         return response()->json(['message' => 'Sport not found'], 404);
    //     }
    //     return response()->json($sport);
    // }

    public function show($id)
    {
        // Find sport with products eagerly loaded
        $sport = ShopBySport::with('products')->find($id);

        if (!$sport) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        // Return only the products (not the sport info)
        return response()->json($sport->products);
    }

    // Create new sport
    public function store(Request $request)
    {
        $request->validate([
            'sportname' => 'required|string|max:255',
            'image' => 'required|url',
            'slide_active' => 'boolean',
        ]);

        $sport = ShopBySport::create([
            'sportname' => $request->sportname,
            'image' => $request->image,
            'slide_active' => $request->slide_active ?? 0,
        ]);

        return response()->json($sport, 201);
    }

    // Update existing sport
    public function update(Request $request, $id)
    {
        $sport = ShopBySport::find($id);
        if (!$sport) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        $request->validate([
            'sportname' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|required|url',
            'slide_active' => 'sometimes|boolean',
        ]);

        $sport->update($request->only(['sportname', 'image', 'slide_active']));

        return response()->json($sport);
    }

    // Delete a sport
    public function destroy($id)
    {
        $sport = ShopBySport::find($id);
        if (!$sport) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        $sport->delete();
        return response()->json(['message' => 'Sport deleted successfully']);
    }
}
