<?php
namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class NewYorkTimesAPISource implements NewsSourceInterface
{
    private const SOURCE_NAME = 'NewYorkTimesAPI';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('NYTAPI_KEY');
        
        if (empty($this->apiKey)) {
            throw new \Exception('NYTAPI_KEY is not set in the .env file');
        }
    }

    public function fetchArticles(array $params = []): array
    {
        $response = Http::get("https://api.nytimes.com/svc/topstories/v2/world.json", [
            'api-key' => $this->apiKey,
        ]);

        if ($response->failed() || !isset($response->json()['results'])) {
            return [];
        }

        return array_map(function ($article) {
            $urlToImage = array_filter($article['multimedia'], function($multimedia) {
                return isset($multimedia['format']) && $multimedia['format'] === 'threeByTwoSmallAt2X';
            });

            $urlToImage = array_values($urlToImage);
            $urlToImage = $urlToImage[0]['url'] ?? null;

            $author = $article['byline'] ?? 'Unknown';
            $author = stripos(trim($author), 'by ') === 0 ? substr(trim($author), 3) : trim($author);

            return [
                'title' => $article['title'],
                'content' => $article['abstract'],
                'author' => $author,
                'source_name' => self::SOURCE_NAME,
                'url' => $article['url'],
                'image_url' => $urlToImage,
                'published_at' => \Carbon\Carbon::parse($article['published_date']),
                'category' => 'General',
            ];
        }, $response->json()['results']);
    }

    public function getSourceName(): string
    {
        return self::SOURCE_NAME;
    }
}