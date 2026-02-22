<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Room;

class PropertyController extends Controller
{
    /// Get property details for a tenant
    public function index()
    {
        $properties = Property::orderBy('id', 'desc')->get();
        return response()->json($properties);
    }

    /// Get specific property details by ID
    public function show($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }
        return response()->json($property);
    }

    /// Get property details for a specific room
    public function getByRoom($roomId)
    {
        $property = Property::where('room_id', $roomId)->first();
        if (!$property) {
            return response()->json([
                'message' => 'Property not found for this room'
            ], 404);
        }
        return response()->json($property);
    }

    /// Create property details for a tenant
    public function store(Request $request, $roomId)
    {
        $request->validate([
            'water' => 'required|numeric',
            'electricity' => 'required|numeric',
            'trash' => 'required|numeric',
            'parking' => 'required|numeric',
        ]);
        $room = Room::find($roomId);
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $property = Property::where('room_id', $roomId)->first();

        if ($property) {
            // update existing property
            $property->update([
                'room_rent' => $room->price ?? 0,
                'water' => $request->water,
                'electricity' => $request->electricity,
                'trash' => $request->trash,
                'parking' => $request->parking,
            ]);
        } else {
            // create new property only if missing
            $property = Property::create([
                'room_id' => $room->id,
                'room_rent' => $room->price ?? 0,
                'water' => $request->water,
                'electricity' => $request->electricity,
                'trash' => $request->trash,
                'parking' => $request->parking,
            ]);
        }
        return response()->json([
            'message' => 'Property created successfully',
            'data' => $property
        ], 201);
    }
}
