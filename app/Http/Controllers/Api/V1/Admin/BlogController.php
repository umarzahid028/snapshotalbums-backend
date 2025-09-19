<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;


class BlogController extends Controller
{
    // List all blogs
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 7);
            $search = $request->get('search');
            $status = $request->get('status', 'all');

            $query = Blog::query()->latest();

            // 🔍 Apply search if provided
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $blogs = $query->paginate($perPage);

            $totalPosts = Blog::count();
            $published = Blog::where('status', 'published')->count();
            $drafts = Blog::where('status', 'draft')->count();
            $totalViews = 0;

            return BlogResource::collection($blogs)
                ->additional([
                    'stats' => [
                        'total_posts' => $totalPosts,
                        'published'   => $published,
                        'drafts'      => $drafts,
                        'total_views' => $totalViews,
                    ],
                    'paginate' => [
                        'current_page' => $blogs->currentPage(),
                        'last_page'    => $blogs->lastPage(),
                        'per_page'     => $blogs->perPage(),
                        'total'        => $blogs->total(),
                    ]
                ]);
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
            'slug' => 'required|string|unique:blogs,slug',
            'description' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'status' => 'nullable|in:draft,published,archived',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:255',
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
    public function update(Request $request, $id)
    {
        try {
            $blog = Blog::where('slug', $id)->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                Rule::unique('blogs', 'slug')->ignore($blog->id), 
            ],
            'description' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'status' => 'nullable|in:draft,published,archived',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:255',
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
                'message' => 'Blog updated successfully',
                'data' => new BlogResource($updatedBlog)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Blog Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update blog'], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Blog Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog. ' . $e->getMessage()
            ], 500);
        }
    }
}
