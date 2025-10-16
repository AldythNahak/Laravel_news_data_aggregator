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
        $listCategory = [
            "business" => [
                "business",
                "economy",
                "money",
                "work",
                "media"
            ],
            "entertainment" => [
                "arts",
                "books",
                "culture",
                "film",
                "fashion",
                "stage",
                "music",
                "lifeandstyle"
            ],
            "general" => [
                "world",
                "uk-news",
                "australia-news",
                "us-news",
                "opinion",
                "travel",
                "education",
                "environment",
                "cities",
                "global-development"
            ],
            "health" => [
                "health",
                "food",
                "society",
                "wellbeing"
            ],
            "science" => [
                "science",
                "space"
            ],
            "sports" => [
                "sport",
                "football",
                "cricket",
                "tennis",
                "rugby-union",
                "golf",
                "formulaone"
            ],
            "technology" => [
                "technology",
                "tech",
                "games",
                "digital"
            ],
        ];

        $articles = [];

        foreach ($listCategory as $category => $topics) {
            foreach ($topics as $topic) {
                $response = Http::get("https://content.guardianapis.com/search", [
                    'api-key' => $this->apiKey,
                    'sections' => $topic
                ]);

                if ($response->failed() || !isset($response->json()['response']['results'])) {
                    echo "- ❌ Failed Fetching $category topic: $topic \n";
                    continue;
                }

                $rawData = $response->json()['response']['results'];
                $result = array_map(function ($article) use ($category) {
                    return [
                        'title' => $article['webTitle'],
                        'content' => $article['webContent'] ?? null,
                        'author' => $article['author'] ?? 'Unknown',
                        'source_name' => self::SOURCE_NAME,
                        'url' => $article['webUrl'],
                        'image_url' => $article['webImageUrl'] ?? null,
                        'published_at' => \Carbon\Carbon::parse($article['webPublicationDate']),
                        'category' => $category,
                    ];
                }, $rawData);

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
