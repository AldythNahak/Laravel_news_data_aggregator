<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($search = $request->query('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($date = $request->query('date')) {
            $query->whereDate('published_at', $date);
        }
        
        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }
        
        if ($sources = $request->query('sources')) {
            $sourceArray = explode(',', $sources);
            $query->whereIn('source_name', $sourceArray);
        }
        
        $articles = $query
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return response()->json($articles);
    }
}
