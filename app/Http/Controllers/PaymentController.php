<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use App\Models\Tenant;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /// Get all payments for a specific room
    public function index()
    {
        $payments = Payment::orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'message' => 'Payments retrieved successfully',
            'data' => $payments
        ]);
    }

    /// Get all payments for a specific tenant
    public function getPaymentsByTenant($tenantId)
    {
        $payments = Payment::orderBy('id', 'DESC')->with('tenant')->where('tenant_id', $tenantId)->get();
        return response()->json([
            'status' => true,
            'message' => 'Payments retrieved successfully',
            'data' => $payments
        ]);
    }

    /// Create a new payment for a tenant
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);

        // Get tenant with room & property
        $tenant = Tenant::with('room.property')->findOrFail($request->tenant_id);

        // Check if tenant already paid for this month/year
        $exists = Payment::where('tenant_id', $tenant->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant has already paid for this month.'
            ], 400);
        }

        // Calculate total amount from properties
        $property = $tenant->room->property;
        $amount = $property->room_rent
            + $property->water
            + $property->electricity
            + $property->trash
            + $property->parking;

        // Create payment
        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'month' => $request->month,
            'year' => $request->year,
            'amount' => $amount,
            'payment_date' => Carbon::now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => $payment
        ]);
    }


    public function tenantPayments($tenantId)
    {
        $payments = Payment::with('period')
            ->where('tenant_id', $tenantId)
            ->get();

        return response()->json($payments);
    }
}
