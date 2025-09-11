<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\FaqResource;

class FaqController extends Controller
{
    // List all FAQs
    public function index()
    {
        try {
            $faqs = Faq::where('is_active', true)->latest()->get();
            return FaqResource::collection($faqs);
        } catch (\Exception $e) {
            Log::error('FAQ Index Error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to fetch FAQs'.$e->getMessage()], 500);
        }
    }

    // Show single FAQ
    public function show($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            return new FaqResource($faq);
        } catch (\Exception $e) {
            Log::error('FAQ Show Error: '.$e->getMessage());
            return response()->json(['error' => 'FAQ not found'.$e->getMessage()], 404);
        }
    }

    // Create FAQ
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $faq = DB::transaction(function () use ($request) {
                return Faq::create($request->only('question', 'answer', 'is_active'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Faq Store successfully',
                'data' => new FaqResource($faq)
            ], 200);
        } catch (\Exception $e) {
            Log::error('FAQ Store Error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to create FAQ'.$e->getMessage()], 500);
        }
    }

    // Update FAQ
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'sometimes|required|string|max:255',
            'answer' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $faq = Faq::findOrFail($id);

            DB::transaction(function () use ($faq, $request) {
                $faq->update($request->only('question', 'answer', 'is_active'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Faq update successfully',
                'data' => new FaqResource($faq)
            ], 200);
        } catch (\Exception $e) {
            Log::error('FAQ Update Error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to update FAQ'.$e->getMessage()], 500);
        }
    }

    // Delete FAQ
    public function destroy($id)
    {
        try {
            $faq = Faq::findOrFail($id);

            DB::transaction(function () use ($faq) {
                $faq->delete();
            });

            return response()->json(['message' => 'FAQ deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('FAQ Delete Error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to delete FAQ'.$e->getMessage()], 500);
        }
    }
}
