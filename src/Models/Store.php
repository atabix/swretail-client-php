<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;

class Store extends Model
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
        $path = 'stores';

        $response = Client::requestApi('GET', $path);

        $stores = [];
        foreach ($response->json as $itemData) {
            $stores[] = new static($itemData->id, $itemData->name);
        }

        return $stores;
    }

    public function getId()
    {
        return $this->id;
    }
}
