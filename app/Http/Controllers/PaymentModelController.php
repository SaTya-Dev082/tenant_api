<?php

namespace App\Http\Controllers;

use App\Models\PaymentModel;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentModelController extends Controller
{
    /// Get all payments for a specific room
    public function index()
    {
        $payments = PaymentModel::orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'message' => 'Payments retrieved successfully',
            'data' => $payments
        ]);
    }

    /// Get all payments for a specific tenant
    public function byTenant($tenantId)
    {
        $payments = PaymentModel::with([
            'months:id,name',
            'years:id,year',
        ])
            ->where('tenant_id', $tenantId)
            ->latest('id')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $payments,
        ]);
    }

    /// Get property details for a tenant's payment
    public function show($paymentId)
    {
        $payment = PaymentModel::with([
            'months:id,name',
            'years:id,year',
            'tenant:id,room_id,name,email,phone_number',
            'tenant.room:id,room_number,price,status',
            'tenant.room.property:id,room_id,room_rent,water,electricity,trash,parking',
        ])->findOrFail($paymentId);

        return response()->json([
            'status' => true,
            'data' => $payment,
        ]);
    }

    /// Sort by month and year
    public function sortByMonthYear(Request $request, $month_id, $year_id)
    {
        $payments = PaymentModel::where('month_id', $month_id)
            ->where('year_id', $year_id)
            ->orderBy('id', 'DESC')
            ->get();
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
            'month_id'  => 'required|exists:months,id',
            'year_id'   => 'required|exists:year_models,id', // default table name for YearModel
        ]);
        // ✅ return with month/year names for UI



        // Get tenant with room & property
        $tenant = Tenant::with('room.property')->findOrFail($request->tenant_id);

        // Check if tenant already paid for this month/year
        $exists = PaymentModel::where('tenant_id', $tenant->id)
            ->where('month_id', $request->month_id)
            ->where('year_id', $request->year_id)
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
        $payment = PaymentModel::create([
            'tenant_id' => $tenant->id,
            'month_id' => $request->month_id,
            'year_id' => $request->year_id,
            'amount' => $amount,
            'payment_date' => Carbon::now()->toDateString(),
        ]);
        $payment->load([
            'months:id,name',
            'years:id,year',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => $payment
        ], 201);
    }
}
