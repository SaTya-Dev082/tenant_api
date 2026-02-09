<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    /// Get property details for a tenant
    public function index()
    {
        $properties = Property::orderBy('id', 'desc')->get();
        return response()->json($properties);
    }

    /// Create property details for a tenant
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'room_rent' => 'required|numeric',
            'water' => 'required|numeric',
            'electricity' => 'required|numeric',
            'trash' => 'required|numeric',
            'moto_parking' => 'required|in:yes,no',
        ]);

        $property = Property::create($request->all());
        return response()->json($property, 201);
    }

    /// Update property details for a tenant
    public function update(Request $request, $id)
    {
        $property = Property::find($id);

        $request->validate([
            'room_rent' => 'sometimes|numeric',
            'water' => 'sometimes|numeric',
            'electricity' => 'sometimes|numeric',
            'trash' => 'sometimes|numeric',
            'moto_parking' => 'sometimes|in:yes,no',
        ]);

        $property->update($request->all());
        return response()->json($property);
    }

    /// Toggle moto parking availability for a tenant
    public function toggleMotoParking($id)
    {
        $properties = Property::find($id);
        if (!$properties) {
            return response()->json(['message' => 'Property not found'], 404);
        }
        // Ensure the property belongs to the authenticated owner
        $properties->moto_parking = $properties->moto_parking === 'yes' ? 'no' : 'yes';
        $properties->save();
        return response()->json($properties);
    }
}
