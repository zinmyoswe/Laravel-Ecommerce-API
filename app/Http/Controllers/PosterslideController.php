<?php


namespace App\Http\Controllers;

use App\Models\Posterslide;
use Illuminate\Http\Request;

class PosterslideController extends Controller
{
    public function index(Request $request)
{
    $query = Posterslide::query();

    // Filter by part if requested
    if ($request->has('part')) {
        $query->where('part', $request->part);
    }

    // Order by posterslideid ascending
    $slides = $query->orderBy('posterslideid', 'asc')->get();

    return response()->json($slides);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'posterslideimage' => 'required|string',
            'posterslidename' => 'required|string',
            'posterslidename2' => 'nullable|string',
            'buttonname' => 'nullable|string',
            'part' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        return Posterslide::create($data);
    }

    public function show($id)
    {
        return Posterslide::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $poster = Posterslide::findOrFail($id);

        $data = $request->validate([
            'posterslideimage' => 'sometimes|required|string',
            'posterslidename' => 'sometimes|required|string',
            'posterslidename2' => 'nullable|string',
            'buttonname' => 'nullable|string',
            'part' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $poster->update($data);
        return $poster;
    }

    public function destroy($id)
    {
        $poster = Posterslide::findOrFail($id);
        $poster->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

