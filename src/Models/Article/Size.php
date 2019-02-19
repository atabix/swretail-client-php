<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;
use SWRetail\Models\Traits\UseDataMap;
use SWRetail\Models\Type\Price;

class Size extends Model
{
    use UseDataMap;

    protected $barcodes = [];

    const DATAMAP = [
        'position'       => 'position',
        'description'    => 'name',
        'stock'          => 'stock',
        'salepricedelta' => 'sale_price_delta',
        'purpricedelta'  => 'purchase_price_delta',
    ];

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function addBarcode(Barcode $barcode)
    {
        $this->barcodes[] = $barcode;

        return $this;
    }

    public function getBarcodes()
    {
        return $this->barcodes;
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'position':
            case 'stock':
                $this->data->$key = (int) $value;
                break;
            case 'sale_price_delta':
            case 'purchase_price_delta':
                $this->data->$key = new Price($value);
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
            'position'    => $this->getPosition(),
            'description' => $this->getName(),
        ];
    }
}
