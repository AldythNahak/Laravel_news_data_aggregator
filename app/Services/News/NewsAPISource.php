<?php
namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class NewsAPISource implements NewsSourceInterface
{
    private const SOURCE_NAME = 'NewsAPI';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('NEWSAPI_KEY');
        
        if (empty($this->apiKey)) {
            throw new \Exception('NEWSAPI_KEY is not set in the .env file');
        }
    }

    public function fetchArticles(array $params = []): array
    {
        $response = Http::get("https://newsapi.org/v2/top-headlines", [
            'apiKey' => $this->apiKey,
            'country' => 'us',
        ]);

        if ($response->failed() || !isset($response->json()['articles'])) {
            return [];
        }

        return array_map(function ($article) {
            return [
                'title' => $article['title'],
                'content' => $article['content'],
                'author' => $article['author'] ?? 'Unknown',
                'source_name' => self::SOURCE_NAME,
                'url' => $article['url'],
                'image_url' => $article['urlToImage'],
                'published_at' => \Carbon\Carbon::parse($article['publishedAt']),
                'category' => 'General',
            ];
        }, $response->json()['articles']);
    }

    public function getSourceName(): string
    {
        return self::SOURCE_NAME;
    }
}