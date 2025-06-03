<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogControllerBE extends Controller
{
    public function get(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $categoryFilter = $request->input('category', '');
            
            $query = Blog::with(['user'])
                ->select('blogs.*')
                ->orderBy('created_at', 'desc');
            
            // Apply search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
                });
            }
            
            // Apply category filter
            if (!empty($categoryFilter)) {
                $query->where('category', $categoryFilter);
            }
            
            // Paginate the results
            $blogs = $query->paginate($perPage);
            
            return response()->json($blogs);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog posts: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    // Get categories for dropdown
    public function getCategories()
    {
        try {
            // Get distinct categories from the blogs table
            $categories = Blog::distinct()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->pluck('category')
                ->toArray();
                
            return response()->json([
                'success' => true,
                'data' => array_map(function($category) {
                    return [
                        'id' => $category,
                        'name' => $category
                    ];
                }, $categories)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function getById($id)
    {
        try {
            $blog = Blog::with(['user'])->find($id);
            
            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog post not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Blog post retrieved successfully',
                'data' => $blog,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog post: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'author' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'slug' => 'required|string|unique:blogs,slug',
                'is_published' => 'boolean',
            ]);
            
            DB::beginTransaction();
            
            $blog = new Blog();
            $blog->title = $request->input('title');
            $blog->content = $request->input('content');
            $blog->author = $request->input('author');
            $blog->category = $request->input('category');
            $blog->slug = $request->input('slug');
            $blog->is_published = $request->input('is_published', 0);
            $blog->user_id = Auth::id();
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/blogs'), $imageName);
                $blog->image = 'uploads/blogs/' . $imageName;
            }
            
            $blog->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully',
                'data' => $blog,
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog post: ' . $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null,
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'author' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'is_published' => 'boolean',
            ]);
            
            $blog = Blog::find($id);
            
            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog post not found',
                ], 404);
            }
            
            DB::beginTransaction();
            
            $blog->title = $request->input('title');
            $blog->content = $request->input('content');
            $blog->author = $request->input('author');
            $blog->category = $request->input('category');
            $blog->is_published = $request->input('is_published', 0);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($blog->image && file_exists(public_path($blog->image))) {
                    unlink(public_path($blog->image));
                }
                
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/blogs'), $imageName);
                $blog->image = 'uploads/blogs/' . $imageName;
            }
            
            $blog->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully',
                'data' => $blog,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog post: ' . $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null,
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $blog = Blog::find($id);
            
            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog post not found',
                ], 404);
            }
            
            // Delete image if exists
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }
            
            $blog->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog post: ' . $e->getMessage(),
            ], 500);
        }
    }
}
