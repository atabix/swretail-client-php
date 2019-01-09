<?php

namespace SWRetail\Models\Order;

use SWRetail\Http\Client;

class OrderChanged
{
    protected $minutes;

    public function __construct(int $minutes)
    {
        if ($minutes > 1500) {
            throw new \OverflowException('Changed data only up to 1500 minutes ago.');
        }
        $this->minutes = $minutes;
    }

    public function get()
    {
        $path = 'order_changed/' . $this->minutes;

        $response = Client::requestApi('GET', $path);

        $orderIds = $response->json;

        return $orderIds;
    }
}
