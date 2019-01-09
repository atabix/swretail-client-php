<?php 

// by SWRetail ID:
$order = Order::get(352489);
// by Webshop ID:
$order = Order::byWebId('8765-fd9a');

// Recent changed orders:
$orderIds = Order::allChanged($minutes = 1500)->get();


// Delete.
$order->delete();
