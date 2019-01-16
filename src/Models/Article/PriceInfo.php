<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;
use SWRetail\Models\Traits\UseDataMap;
use function SWRetail\price_or_percentage;

class PriceInfo extends Model // ModelInfo
{
    use UseDataMap;

    const DATAMAP = [
        'article_price_web'          => 'web',
        'article_basepurprice'       => 'purchase',
        'article_baseprice'          => 'base',
        'article_discount'           => 'discount',
        'article_price_web_discount' => 'web_discount',
        'article_price_wholesale'    => 'wholesale',
        'article_taxrate'            => 'tax_rate',
    ];

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function setMappedValue($apiKey, $value)
    {
        if (! \array_key_exists($apiKey, self::DATAMAP)) {
            throw new \InvalidArgumentException('Invalid map key');
        }
        $property = self::DATAMAP[$apiKey];
        $this->setValue($property, $value);

        return $this;
    }

    public function setValue($key, $value)
    {
        $this->data->$key = price_or_percentage($value);

        return $this;
    }

    protected function getApiValue($key, $value)
    {
        return (string) $value;
    }

    public function toApiRequest()
    {
        return $this->mapDataToApiRequest();
    }
}
