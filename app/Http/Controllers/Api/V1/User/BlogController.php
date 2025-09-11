<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index()
    {
        try {
            $blogs = Blog::latest()->get();
            return BlogResource::collection($blogs);
        } catch (\Exception $e) {
            Log::error('Blog Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch blogs' . $e->getMessage()], 500);
        }
    }

    public function show($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->firstOrFail();
            return new BlogResource($blog);
        } catch (\Exception $e) {
            Log::error('Blog Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Blog not found' . $e->getMessage()], 404);
        }
    }
}
