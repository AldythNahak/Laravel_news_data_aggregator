<?php

namespace App\Services\News;

interface NewsSourceInterface
{
    public function fetchArticles(array $params = []): array; 
    public function getSourceName(): string;
}
