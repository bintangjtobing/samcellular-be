<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Brands;
use Illuminate\Http\Request;

class BrandApiController extends Controller
{
    /**
     * Get all brands for a business.
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
            $brands = Brands::where('business_id', $business_id)
                            ->orderBy('name', 'asc')
                            ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $brands
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dropdown of brands for a business.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dropdown(Request $request)
    {
        $business_id = $request->get('business_id', null);
        $show_none = $request->get('show_none', false);
        $filter_use_for_repair = $request->get('filter_use_for_repair', false);

        if (!$business_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business ID is required.'
            ], 400);
        }

        try {
            $brands = Brands::forDropdown($business_id, $show_none, $filter_use_for_repair);

            return response()->json([
                'success' => true,
                'data' => $brands
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
