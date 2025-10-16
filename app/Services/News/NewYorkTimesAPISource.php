<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class NewYorkTimesAPISource implements NewsSourceInterface
{
    private const SOURCE_NAME = 'NewYorkTimesAPI';
    private string $apiKey;
    private $command;

    public function __construct()
    {
        $this->apiKey = env('NYTAPI_KEY');

        if (empty($this->apiKey)) {
            throw new \Exception('NYTAPI_KEY is not set in the .env file');
        }
    }

    public function fetchArticles(array $params = []): array
    {
        $listCategory = [
            "business" => ["business", "realestate", "politics"],
            "entertainment" => ["arts", "fashion", "movies"],
            "general" => ["home", "world"],
            "health" => ["health"],
            "science" => ["science"],
            "sports" => ["sports"],
            "technology" => ["technology"],
        ];
        $articles = [];
        $counterRequest = 0;

        foreach ($listCategory as $category => $topics) {
            foreach ($topics as $topic) {
                $counterRequest ++;

                if ($counterRequest%5 == 0) {
                    sleep(60);
                }

                $response = Http::get("https://api.nytimes.com/svc/topstories/v2/$topic.json", [
                    'api-key' => $this->apiKey,
                ]);

                if ($response->failed() || !isset($response->json()['results'])) {
                    echo "- ❌ Failed Fetching $category topic: $topic \n";
                    continue;
                }

                $result = array_map(function ($article) use ($category) {
                    $article['multimedia'] = $article['multimedia'] ?? []; 
                    $urlToImage = array_filter($article['multimedia'], function ($multimedia) {
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
                        'category' => $category,
                    ];
                }, $response->json()['results']);

                $articles = array_merge($articles, $result);
            }
        }

        return $articles;
    }

    public function getSourceName(): string
    {
        return self::SOURCE_NAME;
    }
}
