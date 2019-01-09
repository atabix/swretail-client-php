<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;

class CashRegister extends Model
{
    protected $id;
    protected $data;

    public function __construct($id, $data = null)
    {
        $this->id = $id;
        $this->data = new \stdClass();
        if ($data) {
            $this->parseData($data);
        }
    }
    
    public function parseData($data)
    {
        $this->data->name = (string) $data->name;
        $this->data->active = (bool) $data->active;
        $this->data->warehouse_id = $data->warehouse_id ? (int) $data->warehouse_id : null;
        $this->data->store_id = $data->store_id ? (int) $data->store_id : null;
    }

    /**
     * @api
     *
     * @return array[CashRegister]
     */
    public static function getAll(): array
    {
        $path = 'cash_registers';

        $response = Client::requestApi('GET', $path);

        $registers = [];
        foreach ($response->json as $itemData) {
            $registers[] = new static($itemData->id, $itemData);
        }

        return $registers;
    }

    public function getId()
    {
        return $this->id;
    }
}
