<?php

namespace SWRetail\SWRetail\Models\Article;

use SWRetail\Models\Model;

class PriceInfo extends Model // ModelInfo
{
    protected $base;
    protected $purchase;
    protected $discount;
    protected $web;
    protected $web_discount;
    protected $wholesale;
    protected $taxrate;

    private $map = [
        'article_price_web'          => 'web',
        'article_basepurprice'       => 'purchase',
        'article_baseprice'          => 'base',
        'article_discount'           => 'discount',
        'article_price_web_discount' => 'web_discount',
        'article_price_wholesale'    => 'wholesale',
        'article_taxrate'            => 'taxrate',
    ];

    public function setMappedValue($apiKey, $value)
    {
        if (! \array_key_exists($apiKey, $this->map)) {
            throw new \InvalidArgumentException('Invalid map key');
        }
        $property = $this->map[$apiKey];

        $priceValue = (\substr($value, -1, 1) == '%')
            ? new Percentage(\substr($value, 0, \strlen($value) - 1))
            : new Price($value);

        $this->$property = $priceValue;

        return $this;
    }
}
