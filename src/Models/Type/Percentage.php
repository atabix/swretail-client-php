<?php

namespace SWRetail\Models\Type;

class Percentage
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toFloat()
    {
        return $this->value / 100;
    }

    public function __toString()
    {
        return \number_format($this->value, 2);
    }
}
