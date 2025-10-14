<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Services\News\NewsSourceInterface;
use App\Services\News\NewsAPISource;
use App\Services\News\NewYorkTimesAPISource;

class AggregateNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:aggregate-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches articles from all configured news sources and stores them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sources = [
            new NewsAPISource(),
            new NewYorkTimesAPISource(), 
        ];

        foreach ($sources as $source) {
            $this->info("Fetching articles from: " . $source->getSourceName());
            $articlesData = $source->fetchArticles();
            
            $this->storeArticles($articlesData, $source->getSourceName());
            
            $this->info("Stored " . count($articlesData) . " articles from " . $source->getSourceName());
        }

        return self::SUCCESS;
    }

    private function storeArticles(array $articlesData, string $sourceName): void
    {
        foreach ($articlesData as $data) {
            Article::updateOrCreate(
                ['url' => $data['url']],
                $data
            );
        }
    }
}
