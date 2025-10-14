<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'author',
        'source_name',
        'category',
        'url',
        'image_url',
        'published_at',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    
    /**
     * Scope a query to include articles matching a search term in title or content.
     */
    public function scopeSearch(Builder $query, ?string $searchTerm): void
    {
        if ($searchTerm) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }
    }

    /**
     * Scope a query to include articles matching specific categories.
     */
    public function scopeCategory(Builder $query, ?string $categories): void
    {
        if ($categories) {
            $categoryArray = explode(',', $categories);
            $query->whereIn('category', $categoryArray);
        }
    }

    /**
     * Scope a query to include articles from specific sources (User Preferences).
     */
    public function scopeSources(Builder $query, ?string $sources): void
    {
        if ($sources) {
            $sourceArray = explode(',', $sources);
            $query->whereIn('source_name', $sourceArray);
        }
    }
}
