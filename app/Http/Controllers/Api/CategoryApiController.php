<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Get all categories and their subcategories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $business_id = $request->get('business_id', null);

        if (!$business_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business ID is required.'
            ], 400);
        }

        try {
            $categories = Category::catAndSubCategories($business_id);

            return response()->json([
                'success' => true,
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories dropdown for a given business ID and type.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dropdown(Request $request)
    {
        $business_id = $request->get('business_id', null);
        $type = $request->get('type', 'product');

        if (!$business_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business ID is required.'
            ], 400);
        }

        try {
            $categories = Category::forDropdown($business_id, $type);

            return response()->json([
                'success' => true,
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
