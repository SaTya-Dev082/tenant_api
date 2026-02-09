<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /// Get all rooms for the authenticated owner
    public function index()
    {
        $owner = auth()->user();
        $rooms = Room::orderBy('id', "DESC")/*->with('owner')*//*->where('owner_id', $owner->id)*/->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /// Create a new room
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'room_number' => 'required|unique:rooms',
            'price' => 'required|numeric',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validate->errors()
            ], 422);
        }

        $owner = auth()->user();

        $room = Room::create([
            'owner_id' => $owner->id,
            'room_number' => $request->room_number,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'data' => $room
        ], 201);
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
            "price" => "sometimes|numeric",
            "status" => "sometimes|in:available,occupied,maintenance",
        ]);
        if (!$validate->fails()) {
            if ($request->has('room_number')) {
                $room->room_number = $request->room_number;
            }
            if ($request->has('price')) {
                $room->price = $request->price;
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
