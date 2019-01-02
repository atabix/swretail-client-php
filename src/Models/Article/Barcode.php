<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;

class Barcode extends Model
{
    protected $position;
    protected $barcode;

    private $map = [
        'position' => 'position',
        'barcode'  => 'barcode',
    ];

    public function __construct($barcode, int $position = 1)
    {
        $this->barcode = $barcode;
        $this->position = $position;
    }

    public function setBarcode($value)
    {
        $this->barcode = $value;

        return $this;
    }

    public function setPosition(int $value): self
    {
        $this->position = $value;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

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
                case 'position':
                    $sizeValue = (int) $value;
                    break;
                default:
                    $sizeValue = $value;
            }

            $this->$property = $sizeValue;
        }

        return $this;
    }

    public function toApiRequest()
    {
        return [
            'position' => $this->position,
            'barcode'  => $this->barcode,
        ];
    }
}
