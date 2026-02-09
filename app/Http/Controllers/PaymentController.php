<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

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
        $payments = Payment::orderBy('id', 'DESC')->where('tenant_id', $tenantId)->get();
        return response()->json([
            'status' => true,
            'message' => 'Payments retrieved successfully',
            'data' => $payments
        ]);
    }


    /// Create a new payment
    public function createPayment(Request $request)
    {
        $validatedData = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
        ]);

        $payment = Payment::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }
}
