<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Get all products with optional filters
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Ambil semua produk
            $products = Product::with(['brand', 'unit', 'category', 'sub_category', 'variations', 'variations.media'])
                ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                })
                ->when($request->has('category_id'), function ($query) use ($request) {
                    $query->where('category_id', $request->get('category_id'));
                })
                ->when($request->has('brand_id'), function ($query) use ($request) {
                    $query->where('brand_id', $request->get('brand_id'));
                })
                ->when($request->has('limit'), function ($query) use ($request) {
                    $query->limit($request->get('limit'));
                })
                ->get();

            // Return response sebagai JSON
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            // Handle error
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            // Ambil produk berdasarkan ID
            $product = Product::with(['brand', 'unit', 'category', 'sub_category', 'variations', 'variations.media'])
                ->findOrFail($id);

            // Return response sebagai JSON
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            // Handle error jika produk tidak ditemukan atau ada error lain
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}