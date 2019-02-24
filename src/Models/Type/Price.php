<?php

namespace SWRetail\Models\Type;

class Price implements \JsonSerializable
{
    protected $value;
    
    public $decimals = 2;

    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function toFloat()
    {
        return (float) $this->value;
    }
    
    public function jsonSerialize()
    {
        return $this->toFloat();
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
