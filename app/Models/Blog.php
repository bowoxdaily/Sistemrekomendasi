<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'content',
        'author',
        'category',
        'image',
        'slug',
        'is_published',
        'user_id',
    ];
    
    protected $casts = [
        'is_published' => 'boolean',
    ];
    
    /**
     * Get the user that owns the blog post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
