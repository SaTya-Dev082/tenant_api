<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /// Get all rooms for the authenticated owner
    public function index()
    {
        $rooms = Room::orderBy('id', "DESC")->with('photos')->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /// Sort rooms by price
    public function sortByPrice()
    {
        $rooms = Room::orderBy('price', 'DESC')/*->with('photos')*/->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /// Get Rooms of the authenticated owner
    public function getByOwner()
    {
        $owner = auth()->user();
        $rooms = $owner->rooms()->orderBy('id', 'DESC')->with('photos')->get();
        return response()->json([
            'status' => true,
            'data' => $rooms
        ], 200);
    }

    /// Show all photos for a specific room
    public function showPhotos($roomId)
    {
        $room = Room::with('photos')->findOrFail($roomId);

        return response()->json($room);
    }

    /// Show only specific room details (within photos)
    public function showRoom($roomId)
    {
        $room = Room::with('photos')->findOrFail($roomId);

        return response()->json([
            'status' => true,
            'data' => $room
        ], 200);
    }

    // Create room with multiple photos
    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|unique:rooms',
            'status' => 'required|in:available,occupied,maintenance',
            'price' => 'required|numeric',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $owner = auth()->user()->id;

        // Create room
        $room = Room::create([
            'owner_id' => $owner,
            'room_number' => $request->room_number,
            'status' => $request->status,
            'price' => $request->price ?? 0,
            'description' => $request->description ?? null,
        ]);

        // Upload photos if any
        if ($request->hasFile('photos')) {
            foreach ($request->photos as $photo) {
                $image_url = $photo->store('rooms', 'public');
                $room->photos()->create([
                    'photos_path' => "/storage/" . $image_url,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'room' => $room->load('photos')
        ], 201);
    }
    public function showPrice($id)
    {
        $room = Room::with('property')->findOrFail($id);

        return response()->json([
            'room_number' => $room->room_number,
            'price' => $room->price
        ]);
    }

    /// Update an existing room
    public function update(Request $request, $id)
    {

        $room = Room::find($id);
        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }
        $owner = auth()->user()->id;
        if ($room->owner_id !== $owner) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this room'
            ], 403);
        }
        $validate = Validator::make($request->all(), [
            "room_number" => "sometimes|string|max:255",
            "status" => "sometimes|in:available,occupied,maintenance",
        ]);
        if (!$validate->fails()) {
            if ($request->has('room_number')) {
                $room->room_number = $request->room_number;
            }
            if ($request->has('status')) {
                $room->status = $request->status;
            }
            $room->save();
            return response()->json([
                "status" => true,
                "message" => "Room Updated Successfully",
                "room" => $room
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Validation Error",
                "error" => $validate->errors()
            ], 422);
        }
    }

    /// Delete a room
    public function destroy($id)
    {
        $room = Room::find($id);
        $owner = auth()->user()->id;
        if ($room->owner_id !== $owner) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this room'
            ], 403);
        }

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }
        $room->delete();
        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ], 200);
    }
}
