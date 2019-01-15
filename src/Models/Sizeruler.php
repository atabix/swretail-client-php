<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;
use SWRetail\Models\Article\Size;

class Sizeruler extends Model
{
    protected $id;
    protected $name;

    public function __construct($id, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @api
     *
     * @return array[self]
     */
    public static function getAll(): array
    {
        $path = 'sizerulers';

        $response = Client::requestApi('GET', $path);

        $sizerulers = [];
        foreach ($response->json as $itemData) {
            $sizerulers[] = new static($itemData->id, $itemData->name);
        }

        return $sizerulers;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get sizes of a sizeruler.
     *
     * @api
     *
     * @return array[Size]
     */
    public function getSizes()
    {
        $path = 'sizerulers/' . $this->id;

        $response = Client::requestApi('GET', $path);

        $sizes = [];
        foreach ($response->json as $sizeData) {
            $size = (new Size())->setPosition($sizeData->position)->setName($sizeData->name);
            $sizes[] = $size;
        }

        return $sizes;
    }
}
