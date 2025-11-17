<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;

class BoxController extends Controller
{
    // Return all boxes as JSON
    public function index()
    {
        $boxes = Box::all(['id', 'code', 'latitude', 'longitude', 'status']);

        return response()->json($boxes);
    }
}
