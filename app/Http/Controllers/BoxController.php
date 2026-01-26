<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;

class BoxController extends Controller
{
    // Return all boxes as JSON
    public function index()
    {
        $boxes = Box::all(['id', 'code', 'latitude', 'longitude', 'status', 'nameOfConsumer', 'numberOfConsumer']);

        return response()->json($boxes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:boxes',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nameOfConsumer' => 'nullable|string|max:255',
            'numberOfConsumer' => 'nullable|string|max:255',
            'status' => 'required|in:read,not_read,season',
        ]);

        $box = Box::create($validated);

        return response()->json($box, 201);
    }

    public function update(Request $request, $id)
    {
        $box = Box::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:boxes,code,' . $id,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nameOfConsumer' => 'nullable|string|max:255',
            'numberOfConsumer' => 'nullable|string|max:255',
            'status' => 'required|in:read,not_read,season',
        ]);

        $box->update($validated);

        return response()->json($box);
    }
}
