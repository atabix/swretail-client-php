<?php

use SWRetail\Models\Order;

// Retrieve existing order,
$order = Order::byWebId('8765-fd9a');
// or by SW internal order id.
$order = Order::get(352492);

// Make your changes (not to the orderlines!)
$order->setStatus('paid');
// for more, see order_create

$orderId = $order->update();
