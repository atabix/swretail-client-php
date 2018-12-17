<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;

class Warehouse extends Model
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
     * @return array[Warehouse]
     */
    public static function getAll(): array
    {
        $path = 'warehouses';

        $response = Client::requestApi('GET', $path);

        $warehouses = [];
        foreach ($response->json as $itemData) {
            $warehouses[] = new static($itemData->id, $itemData->warehouse);
        }

        return $warehouses;
    }

    public function getId()
    {
        return $this->id;
    }
}
