<?php

namespace SWRetail\Models\Type;

use function SWRetail\price_or_percentage;

class Percentage
{
    protected $value;
    
    public $symbol = false;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toFloat()
    {
        return $this->value / 100;
    }
    
    public function ofPrice($value)
    {
        $price = price_or_percentage($value);
        return new Price($price->toFloat() * $this->toFloat());
    }

    public function __toString()
    {
        return \number_format($this->value, 2) . ($this->symbol ? '%' : '') ;
    }
}
