<?php

namespace SWRetail\Models\Report;

use SWRetail\Http\Client;
use SWRetail\Models\CashRegister;
use SWRetail\Models\Store;
use SWRetail\Models\Traits\UseDataMap;
use SWRetail\Models\Type\Price;

class Payment extends DateReport
{
    use UseDataMap;

    // apikey => localkey
    const DATAMAP = [
        'cash_id'      => 'cash_register_id',
        'store_id'     => 'store_id',
        'amount'       => 'amount',
        'payment_id'   => 'payment_id',
        'payment_type' => 'payment_type',
    ];

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function getAll()
    {
        $this->requireDate();

        $path = 'report_payments/' . $this->formatDate();

        $response = Client::requestApi('GET', $path);

        $list = [];
        foreach ($response->json as $itemData) {
            $item = new static();
            $item->parseData($itemData);
            $list[] = $item;
        }

        return $list;
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'cash_register_id':
                $this->cashRegister = new CashRegister($value);
                break;
            case 'store_id':
                $this->store = new Store($value);
                break;
            case 'payment_id':
                $this->data->$key = (int) $value;
                break;
            case 'amount':
                $this->data->$key = new Price($value);
                break;
            case 'payment_type':
                $this->data->$key = (string) $value;
                break;
            default:
                // ignore
        }

        return $this;
    }
}
