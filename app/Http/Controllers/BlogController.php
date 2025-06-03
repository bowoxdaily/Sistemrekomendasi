<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index()
    {
        return view('dashboard.operator.blog.index');
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        return view('dashboard.operator.blog.create');
    }

    /**
     * Show the form for editing a blog.
     */
    public function edit($id)
    {
        // Pass the ID to the view
        return view('dashboard.operator.blog.edit', [
            'blogId' => $id
        ]);
    }

    /**
     * Display a blog post to public users
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->with('user')
            ->firstOrFail();
            
        // Get recent posts for sidebar
        $recentPosts = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get related posts by category
        $relatedPosts = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->where('category', $blog->category)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        return view('blog.show', compact('blog', 'recentPosts', 'relatedPosts'));
    }
    
    /**
     * Display a listing of blog posts to public users
     */
    public function list(Request $request)
    {
        $category = $request->query('category');
        $search = $request->query('search');
        
        $query = Blog::where('is_published', true)
            ->with('user')
            ->orderBy('created_at', 'desc');
            
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $blogs = $query->paginate(9);
        $categories = Blog::distinct()->pluck('category')->filter()->values();
        
        return view('blog.index', compact('blogs', 'categories', 'category', 'search'));
    }
}
