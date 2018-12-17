<?php

namespace SWRetail\Models\Article;

use SWRetail\Http\Client;

class ArticleChanged
{
    protected $minutes;

    public function __construct(int $minutes)
    {
        if ($minutes > 1500) {
            throw new OverflowException('Changed data only up to 1500 minutes ago.');
        }
        $this->minutes = $minutes;
    }

    public function get()
    {
        $path = 'article_changed/' . $this->minutes;

        $response = Client::requestApi('GET', $path);

        $articleIds = $response->json;

        return $articleIds;
    }
}
