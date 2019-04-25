<?php 

use SWRetail\Models\Article;
use SWRetail\Models\Article\Barcode;
use SWRetail\Models\Order;
use SWRetail\Models\Order\Line;
use SWRetail\Models\Relation;
use SWRetail\Models\Type\Percentage;
use SWRetail\Models\Type\Price;

// Need an existing relation to create an order.
$relation = Relation::byCode('K100135');

// Initialize with your ordernumber.
$order = new Order('8765-fda1');

// Required values (some can be empty, but must be set):
$order->setStatus('created')
    ->setDate(Carbon::now()) // or valid string, also sets 'time';
    ->setShipTo($relation) // Relation object
    ->setShippingCost(new Price('4.95'))
    ->setShipper('PostNL')
    ->setTracker('TR5003')
    ->setInvoiceTo($relation) // Relation object
    ->setPaymentMethod('iDeal')
    ->setPaymentStatus('pending')
    ->setRemark('Lorem ipsum dolor sid amet.');

// optional values:
$order
    ->setWholesale(false);

// (1) Add an existing Article as orderline.
$article = Article::get(38);
$position = 1;
$amount = 1;

$line = Line::fromArticle($article, $position, $amount);

// - do some custom pricing (optional).
$articlePrice = $article->priceInfo()->getWeb();
$discountPrice = (new Percentage('10'))->ofPrice($articlePrice);
$discountedPrice =  $articlePrice->toFloat() - $discountPrice->toFloat();
$line->setDiscount($discountPrice, $discountedPrice * $amount);

// - add to order.
$order->addLine($line);

// (2) Add custom article,  not from the system.
$line = Line::freeArticle("Actie kraslot", 1);
$line->setTaxRate(new Percentage('21.00'));
$line->setLineTotal(new Price('1.50'));
$order->addLine($line);

// (3) Add item by barcode.
$line = Line::fromBarcode(new Barcode('0000001079'), 1);
$order->addLine($line);

// Finally: CREATE
$orderId = $order->create();
