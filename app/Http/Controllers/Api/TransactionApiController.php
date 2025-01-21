<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;

class TransactionApiController extends Controller
{
    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'business_id' => 'required|integer',
            'type' => 'required|string',
            'location_id' => 'required|integer',
            'status' => 'required|string',
            'contact_id' => 'required|integer',
            'transaction_date' => 'required|date',
            'final_total' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|integer',
            'is_direct_sale' => 'nullable|boolean',
            'shipping_charges' => 'nullable|numeric|min:0',
        ]);

        // Calculate tax details
        $final_total = $request->get('final_total');
        $tax_rate = 0.1; // Default tax rate 10%
        $total_before_tax = $final_total / (1 + $tax_rate);
        $tax = $final_total - $total_before_tax;

        // Create transaction using TransactionUtil
        $transaction = $this->transactionUtil->createSellTransaction(
            $validatedData['business_id'],
            $request->all(),
            [
                'total_before_tax' => round($total_before_tax, 2), // Rounded for precision
                'tax' => round($tax, 2)
            ],
            auth()->id()
        );

        // Return response
        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ], 201);
    }
}