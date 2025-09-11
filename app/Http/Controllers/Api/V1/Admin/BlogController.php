<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    // List all blogs
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

    // Show single blog by slug
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

    // Store new blog
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blogs,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        try {
            $blog = DB::transaction(function () use ($data) {
                return Blog::create($data);
            });

            return response()->json([
                'success' => true,
                'message' => 'Blog Store successfully',
                'data' => new BlogResource($blog)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Blog Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create blog' . $e->getMessage()], 500);
        }
    }

    // Update blog
    public function update(Request $request, $slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|unique:blogs,slug,' . $blog->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        try {
            $updatedBlog = DB::transaction(function () use ($blog, $data) {
                $blog->update($data);
                return $blog;
            });

            return response()->json([
                'success' => true,
                'message' => 'Blog update successfully',
                'data' => new BlogResource($updatedBlog)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Blog Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update blog' . $e->getMessage()], 500);
        }
    }

    // Delete blog
    public function destroy($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->firstOrFail();

            DB::transaction(function () use ($blog) {
                $blog->delete();
            });

            return response()->json(['message' => 'Blog deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Blog Delete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete blog' . $e->getMessage()], 500);
        }
    }
}
