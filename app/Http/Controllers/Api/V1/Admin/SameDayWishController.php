<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SameDayWish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SameDayWishController extends Controller
{
    public function index()
    {
        try {
            $wishes = SameDayWish::where('is_active', true)
                ->orderBy('order')
                ->get();
            return response()->json($wishes, 200);
        } catch (\Exception $e) {
            Log::error('Same Day Wishes Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch same day wishes'], 500);
        }
    }

    public function all()
    {
        try {
            $wishes = SameDayWish::orderBy('order')->get();
            return response()->json($wishes, 200);
        } catch (\Exception $e) {
            Log::error('Same Day Wishes All Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch same day wishes'], 500);
        }
    }

    public function show($id)
    {
        try {
            $wish = SameDayWish::findOrFail($id);
            return response()->json($wish, 200);
        } catch (\Exception $e) {
            Log::error('Same Day Wish Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Same day wish not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'messages' => 'required|array',
            'image' => 'required|string',
            'is_active' => 'boolean',
            'order' => 'numeric',
        ]);

        try {
            $wish = DB::transaction(function () use ($request) {
                return SameDayWish::create($request->all());
            });

            return response()->json([
                'success' => true,
                'message' => 'Same day wish created successfully',
                'data' => $wish
            ], 201);
        } catch (\Exception $e) {
            Log::error('Same Day Wish Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create same day wish'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string|max:255',
            'messages' => 'sometimes|required|array',
            'image' => 'sometimes|required|string',
            'is_active' => 'boolean',
            'order' => 'numeric',
        ]);

        try {
            $wish = SameDayWish::findOrFail($id);

            DB::transaction(function () use ($wish, $request) {
                $wish->update($request->all());
            });

            return response()->json([
                'success' => true,
                'message' => 'Same day wish updated successfully',
                'data' => $wish->fresh()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Same Day Wish Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update same day wish'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $wish = SameDayWish::findOrFail($id);

            DB::transaction(function () use ($wish) {
                $wish->delete();
            });

            return response()->json(['message' => 'Same day wish deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Same Day Wish Delete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete same day wish'], 500);
        }
    }
}
