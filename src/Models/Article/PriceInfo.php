<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;
use function SWRetail\price_or_percentage;
use function SWRetail\snake_case;

class PriceInfo extends Model // ModelInfo
{
    protected $base;
    protected $purchase;
    protected $discount;
    protected $web;
    protected $web_discount;
    protected $wholesale;
    protected $tax_rate;

    private $map = [
        'article_price_web'          => 'web',
        'article_basepurprice'       => 'purchase',
        'article_baseprice'          => 'base',
        'article_discount'           => 'discount',
        'article_price_web_discount' => 'web_discount',
        'article_price_wholesale'    => 'wholesale',
        'article_taxrate'            => 'tax_rate',
    ];

    public function setMappedValue($apiKey, $value)
    {
        if (! \array_key_exists($apiKey, $this->map)) {
            throw new \InvalidArgumentException('Invalid map key');
        }
        $property = $this->map[$apiKey];
        $this->$property = price_or_percentage($value);

        return $this;
    }

    public function toApiRequest()
    {
        $map = \array_flip($this->map);
        $data = [];
        foreach ($map as $property => $apiKey) {
            if (! empty($this->$property)) {
                $data[$apiKey] = (string) $this->$property;
            }
        }

        return $data;
    }

    public function __call($name, $arguments)
    {
        if (\substr($name, 0, 3) == 'get') {
            $propertyName = snake_case(\substr($name, 3));
            if (\property_exists($this, $propertyName)) {
                return $this->$propertyName;
            }
        } elseif (\substr($name, 0, 3) == 'set') {
            $propertyName = snake_case(\substr($name, 3));
            if (\property_exists($this, $propertyName)) {
                $value = \reset($arguments);
                $this->$propertyName = price_or_percentage($value);

                return $this;
            }
        }

        throw new \BadMethodCallException("Call to undefined method '$name' in " . __FILE__ . ':' . __LINE__);
    }
}
