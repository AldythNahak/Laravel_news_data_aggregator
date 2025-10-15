<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();
        Log::info('✅ ArticleController@index triggered');

        $query->search($request->query('search'));
        $query->category($request->query('category'));
        $query->sources($request->query('sources'));

        if ($date = $request->query('date')) {
            $query->whereDate('published_at', $date);
        }
        
        $articles = $query
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return response()->json($articles);
    }
}
