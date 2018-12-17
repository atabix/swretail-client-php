<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;

class Action extends Model
{
    protected $startDate;
    protected $endDate;
    protected $discount;
    protected $discountName;

    private $map = [
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
            if (! \array_key_exists($apiKey, $this->map)) {
                throw new \InvalidArgumentException('Invalid map key');
            }
            $property = $this->map[$apiKey];

            switch ($property) {
                case 'startDate':
                    $value = \DateTime::createFromFormat('Ymd His', "$value 000000");
                    break;
                case 'endDate':
                    $value = \DateTime::createFromFormat('Ymd His', "$value 235959");
                    break;
                case 'discount': // Price/Percentage?
                default:
                    // no change.
            }

            $this->$property = $value;
        }

        return $this;
    }

    public function getPosition()
    {
        return $this->order;
    }
}
