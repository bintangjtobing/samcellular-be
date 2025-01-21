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
        'created_by' => 'nullable|integer',
        'products' => 'required|array', // Include products
        'products.*.product_id' => 'required|integer',
        'products.*.variation_id' => 'required|integer',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.unit_price' => 'required|numeric|min:0',
        'products.*.unit_price_inc_tax' => 'required|numeric|min:0',
        'products.*.line_discount_type' => 'nullable|string|in:fixed,percentage',
        'products.*.line_discount_amount' => 'nullable|numeric|min:0',
        'products.*.tax_id' => 'nullable|integer',
        'products.*.sell_line_note' => 'nullable|string',
        'products.*.res_service_staff_id' => 'nullable|integer',
        'products.*.warranty_id' => 'nullable|integer',
    ]);

    $created_by = 3; // Default created_by to 1 if not provided

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
            'total_before_tax' => round($total_before_tax, 2),
            'tax' => round($tax, 2)
        ],
        $created_by
    );

    // Add sell lines to transaction
    $this->transactionUtil->createOrUpdateSellLines(
        $transaction,
        $validatedData['products'],
        $validatedData['location_id']
    );

    // Return response
    return response()->json([
        'success' => true,
        'transaction' => $transaction->load('sell_lines') // Include sell lines in the response
    ], 201);
}

}