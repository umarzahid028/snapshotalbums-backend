<?php

namespace App\Http\Controllers\Api\V1\User;

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
            Log::error('FAQ Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch FAQs'.$e->getMessage()], 500);
        }
    }

}
