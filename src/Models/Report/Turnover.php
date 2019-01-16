<?php

namespace SWRetail\Models\Report;

use SWRetail\Http\Client;
use SWRetail\Models\CashRegister;
use SWRetail\Models\Store;
use SWRetail\Models\Traits\UseDataMap;
use SWRetail\Models\Type\Percentage;
use SWRetail\Models\Type\Price;

class Turnover extends DateReport
{
    use UseDataMap;

    // apikey => localkey
    const DATAMAP = [
        'cash_id'      => 'cash_register_id',
        'store_id'     => 'store_id',
        'turnover'     => 'turnover',
        'tax_percent'  => 'tax_rate',
    ];

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function getAll()
    {
        $this->requireDate();

        $path = 'report_turnover/' . $this->formatDate();

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
            case 'turnover':
                $this->data->$key = new Price($value);
                break;
            case 'tax_rate':
                $this->data->$key = new Percentage($value);
                break;
            default:
                // ignore
        }

        return $this;
    }
}
