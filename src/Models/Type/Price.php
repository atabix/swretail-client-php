<?php

namespace SWRetail\Models\Type;

class Price
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return \number_format($this->value, 2);
    }
}
