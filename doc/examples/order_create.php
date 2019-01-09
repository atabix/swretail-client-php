<?php 

// Need an existing relation to create an order.
$relation = Relation::byCode('K100135');

// Initialize with your ordernumber.
$order = new Order('8765-fda1');

// Required values:
$order->setState('created')
    ->setDate(Carbon::now()) // or valid string, also sets 'time';
    ->setShipTo($relation) // Relation object
    ->setInvoiceTo($relation) // Relation object
    ->setPaymentMethod('iDeal');

// optional values:
$order
    ->setTracker('TR5003')
    ->setWholesale(false)
    ->setShipper('PostNL')
    ->setRemark('Lorem ipsum dolor sid amet.')
    ->setPaymentStatus('pending')
    ->setShippingCost('4.95'); // or Price object

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
