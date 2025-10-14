<?php
namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class TheGuardianAPISource implements NewsSourceInterface
{
    private const SOURCE_NAME = 'TheGuardianAPI';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('THEGUARDIANAPI_KEY');
        
        if (empty($this->apiKey)) {
            throw new \Exception('THEGUARDIANAPI_KEY is not set in the .env file');
        }
    }

    public function fetchArticles(array $params = []): array
    {
        $response = Http::get("https://content.guardianapis.com/search", [
            'api-key' => $this->apiKey,
        ]);

        if ($response->failed() || !isset($response->json()['response']['results'])) {
            return [];
        }

        $results = $response->json()['response']['results'];

        return array_map(function ($article) {
            return [
                'title' => $article['webTitle'],
                'content' => $article['webContent'] ?? null,
                'author' => $article['author'] ?? 'Unknown',
                'source_name' => self::SOURCE_NAME,
                'url' => $article['webUrl'],
                'image_url' => $article['webImageUrl'] ?? null,
                'published_at' => \Carbon\Carbon::parse($article['webPublicationDate']),
                'category' => 'General',
            ];
        }, $results);
    }

    public function getSourceName(): string
    {
        return self::SOURCE_NAME;
    }
}