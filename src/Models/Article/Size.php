<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;
use SWRetail\Models\Type\Price;
use SWRetail\Models\Traits\UseDataMap;

class Size extends Model
{
    use UseDataMap;
    
    protected $position;
    protected $description;
    protected $barcode;

    protected $stock;
    protected $salePriceDelta;
    protected $purchasePriceDelta;

    const DATAMAP = [
        'position'       => 'position',
        'description'    => 'description',
        'stock'          => 'stock',
        'salepricedelta' => 'salePriceDelta',
        'purpricedelta'  => 'purchasePriceDelta',
        'barcode'        => 'barcode',
    ];

    public static function barcode($barcode): self
    {
        $size = new static();

        return $size->setBarcode($barcode);
    }

    public function setBarcode($value)
    {
        $this->barcode = $value;

        return $this;
    }

    public function setName($value) : self
    {
        $this->description = $value;

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
            if (! \array_key_exists($apiKey, self::DATAMAP)) {
                throw new \InvalidArgumentException('Invalid map key');
            }
            $property = self::DATAMAP[$apiKey];

            switch ($property) {
                case 'salePriceDelta':
                case 'purchasePriceDelta':
                    $sizeValue = new Price($value);
                    break;
                case 'position':
                case 'stock':
                    $sizeValue = (int) $value;
                    break;
                default:
                    $sizeValue = $value;
            }

            $this->$property = $sizeValue;
        }

        return $this;
    }

    public function toApiRequest($key = 'sizes')
    {
        if ($key == 'barcodes') {
            return [
                'position' => $this->position,
                'barcode'  => $this->barcode,
            ];
        }

        return [
            'position'    => $this->position,
            'description' => $this->description,
        ];
    }
}
