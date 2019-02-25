<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;
use SWRetail\Models\Traits\UseDataMap;

class Barcode extends Model
{
    use UseDataMap;

    const DATAMAP = [
        'position' => 'position',
        'barcode'  => 'barcode',
    ];

    public function __construct($barcode = null, int $position = 1)
    {
        $this->data = new \stdClass();

        if (! \is_null($barcode)) {
            $this->setBarcode($barcode);
            $this->setPosition($position);
        }
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'position':
                $this->data->$key = (int) $value;
                break;
            default:
                $this->data->$key = (string) $value;
        }

        return $this;
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
            if (! \array_key_exists($apiKey, self::DATAMAP)) {
                throw new \InvalidArgumentException('Invalid map key');
            }
            $property = self::DATAMAP[$apiKey];

            $this->setValue($property, $value);
        }

        return $this;
    }

    public function toApiRequest()
    {
        return [
            'position' => $this->getPosition(),
            'barcode'  => $this->getBarcode(),
        ];
    }

    public function __toString()
    {
        return $this->getBarcode();
    }
}
