<?php

namespace SWRetail\Models\Type;

class Price
{
    protected $value;
    
    public $decimals = 2;

    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function toFloat()
    {
        return $this->value;
    }

    public function __toString()
    {
        return \number_format($this->value, $this->decimals);
    }
}
