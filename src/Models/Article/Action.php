<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;
use function SWRetail\price_or_percentage;

class Action extends Model
{
    protected $startDate;
    protected $endDate;
    protected $discount;
    protected $discountName;

    const DATAMAP = [
        'startdate'    => 'startDate',
        'enddate'      => 'endDate',
        'discount'     => 'discount',
        'discountname' => 'discountName',
    ];

    /**
     * Set values from API response data.
     *
     * @param object|array $values [description]
     *
     * @return self
     */
    public function setMappedValues($values): self
    {
        foreach ($values as $apiKey => $value) {
            if (! \array_key_exists($apiKey, self::DATAMAP)) {
                throw new \InvalidArgumentException('Invalid map key');
            }
            $property = self::DATAMAP[$apiKey];

            switch ($property) {
                case 'startDate':
                    $value = \DateTime::createFromFormat('Ymd His', "$value 000000");
                    break;
                case 'endDate':
                    $value = \DateTime::createFromFormat('Ymd His', "$value 235959");
                    break;
                case 'discount':
                    $value = price_or_percentage($value);
                    break;
                default:
                    // no change.
            }

            $this->$property = $value;
        }

        return $this;
    }

    public function getDescription()
    {
        return $this->discountName;
    }

    public function getDiscount()
    {
        return price_or_percentage($this->discount);
    }
}
