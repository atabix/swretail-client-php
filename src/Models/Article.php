<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;
use SWRetail\Models\Article\MetaInfo;
use SWRetail\Models\Article\PriceInfo;

class Article extends Model
{
    public $data = [];

    protected $metaInfo;
    protected $priceInfo;

    protected $map = [
        //
    ];

    public function __construct($articleNumber = null, $season = 0)
    {
        $this->setValue('number', $articleNumber);
        $this->setValue('season', $season);

        $this->metaInfo = new MetaInfo();
        $this->priceInfo = new PriceInfo();

        //
    }

    public function setValue($name, $value)
    {
        //
    }

    public static function get(int $id) : self
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Articles must have positive IDs.');
        }
        $path = 'article/' . $id;

        $response = Client::requestApi('GET', $path);
        $data = $response->json;

        $article = new static($data->article_number, $data->article_season);

        $article->data = $data; // TEMP
        $this->parseData($data);

        return $article;
    }

    protected function parseData($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'article_metatitle':
                case 'article_metadescription':
                case 'article_metakeywords':
                    $this->metaInfo->setValue(\substr($key, 12), $value);
                    break;
                case 'article_price_web':
                case 'article_basepurprice':
                case 'article_baseprice':
                case 'article_discount':
                case 'article_price_web_discount':
                case 'article_price_wholesale':
                case 'article_taxrate':
                    $this->priceInfo->setMappedValue($key, $value);
                    break;
                case 'article_weight':

            }
        }
    }

    // getPriceInfo()
    public function priceInfo(): PriceInfo
    {
        return $this->priceInfo;
    }

    // getMetaInfo()
    public function metaInfo(): MetaInfo
    {
        return $this->metaInfo;
    }

    public function isWebshop(): bool
    {
        //
    }

    public function getCategory() // : string|array
    {
        //
    }

    public function getDescription(): string
    {
        return $this->data->article_description;
    }

    public function setDescription(string $value): self
    {
        $this->data->article_description = $value;

        return $this;
    }

    // etc.
    //

    public function getSizes(): array
    {
        //
    }

    public function getImages(): array
    {
        //
    }

    public function getActions(): array
    {
        //
    }
}
