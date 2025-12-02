<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomePageContentController extends Controller
{
    public function index()
    {
        try {
            $contents = HomePageContent::orderBy('order')->get();
            return response()->json($contents, 200);
        } catch (\Exception $e) {
            Log::error('Home Page Content Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch home page contents'], 500);
        }
    }

    public function show($id)
    {
        try {
            $content = HomePageContent::findOrFail($id);
            return response()->json($content, 200);
        } catch (\Exception $e) {
            Log::error('Home Page Content Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Content not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'section' => 'required|string|unique:home_page_contents,section',
            'content' => 'required|array',
            'is_active' => 'boolean',
            'order' => 'numeric',
        ]);

        try {
            $content = DB::transaction(function () use ($request) {
                return HomePageContent::create($request->all());
            });

            return response()->json([
                'success' => true,
                'message' => 'Home page content created successfully',
                'data' => $content
            ], 201);
        } catch (\Exception $e) {
            Log::error('Home Page Content Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create content'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section' => 'sometimes|required|string',
            'content' => 'sometimes|required|array',
            'is_active' => 'boolean',
            'order' => 'numeric',
        ]);

        try {
            $content = HomePageContent::findOrFail($id);

            DB::transaction(function () use ($content, $request) {
                $content->update($request->all());
            });

            return response()->json([
                'success' => true,
                'message' => 'Home page content updated successfully',
                'data' => $content->fresh()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Home Page Content Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update content'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $content = HomePageContent::findOrFail($id);

            DB::transaction(function () use ($content) {
                $content->delete();
            });

            return response()->json(['message' => 'Home page content deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Home Page Content Delete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete content'], 500);
        }
    }
}
