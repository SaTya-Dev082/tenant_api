<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    /// Get all tenants for the authenticated owner
    public function index()
    {
        $tenants = Tenant::orderBy('id', "DESC")/*->with('room')*/->get();
        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    /// Get by Owner
    public function getByOwner()
    {
        $owner = auth()->user();
        $tenants = Tenant::orderBy('id', "DESC")->with('room')->whereHas('room', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })->get();
        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    /// Add new Tenant
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id|unique:tenants,room_id',
            'name' => 'required|string',
            'email' => 'required|email|unique:tenants,email',
            'phone_number' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validate->errors()
            ], 422);
        } else {
            if ($request->hasFile('image_path')) {
                $image = $request->file('image_path')->store('tenants', 'public');
                $imagePath = '/storage/' . $image;
            } else {
                $imagePath = null;
            }
            $tenant = Tenant::create([
                'room_id' => $request->room_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'image_path' => $imagePath
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => $tenant
            ], 201);
        }
    }

    /// Update an existing tenant
    public function update(Request $request, $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }
        if ($request->has('room_id')) {
            $tenant->room_id = $request->room_id;
        }
        if ($request->has('name')) {
            $tenant->name = $request->name;
        }
        if ($request->has('email')) {
            $tenant->email = $request->email;
        }
        if ($request->has('phone_number')) {
            $tenant->phone_number = $request->phone_number;
        }
        if ($request->has('start_date')) {
            $tenant->start_date = $request->start_date;
        }
        if ($request->has('end_date')) {
            $tenant->end_date = $request->end_date;
        }
        if ($request->hasFile('image_path')) {
            $oldImage = substr($tenant->image_path, 9);
            Storage::disk("public")->delete($oldImage);
            $image = $request->file("image_path")->store("tenants", "public");
            $imagePath = "/storage/" . $image;
            $tenant->image_path = $imagePath;
        }
        $tenant->save();
        return response()->json([
            'success' => true,
            'message' => 'Tenant updated successfully',
            'data' => $tenant
        ], 200);
    }

    /// Delete a tenant
    public function destroy($id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }
        if ($tenant->image_path) {
            $oldImage = substr($tenant->image_path, 9);
            Storage::disk("public")->delete($oldImage);
        }
        $tenant->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully'
        ], 200);
    }
}
