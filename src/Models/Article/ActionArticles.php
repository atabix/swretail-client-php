<?php

namespace SWRetail\Models\Article;

use SWRetail\Http\Client;

class ActionArticles
{
    protected $date;

    public function __construct()
    {
        //
    }

    public function atDate($date)
    {
        if ($date instanceof \DateTime) {
            $this->date = $date;

            return $this;
        }
        $this->date = \DateTime::createFromFormat('U', \strtotime($date));

        return $this;
    }

    public function get()
    {
        $path = 'article_actions/' . $this->date->format('Ymd');

        $response = Client::requestApi('GET', $path);

        $articleIds = $response->json;

        return $articleIds;
    }
}
