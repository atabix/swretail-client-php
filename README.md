# PHP client for the SW-Retail API

This package provides a PHP client for the API of the SW-Retail cloud. 

For information about getting an SW-Retail account, visit https://www.swretail.nl/ (Dutch).


## Some code examples

**Example: Get an article, and update it.**
```php
<?php
use SWRetail\Models\Article;

$article = Article::get(15);

echo $article->getDescription();
echo $article->priceInfo()->getBase();

$article->setAdditionalInfo('Lorem ipsum dolor sid amet.');
$article->update();
```

**Example: Create an order**
```php
<?php
use SWRetail\Models\Article;
use SWRetail\Models\Article\Barcode;
use SWRetail\Models\Order;
use SWRetail\Models\Order\Line;
use SWRetail\Models\Relation;
use SWRetail\Models\Type\Price;

$relation = Relation::byCode('K100123');

$order = new Order('87654');

$order->setStatus('created')
    ->setDate('2019-01-28')
    ->setShipTo($relation)
    ->setShippingCost(new Price('4.95'))
    ->setInvoiceTo($relation)
    ->setPaymentMethod('iDeal');
    // Set some more properties, see example doc.

$order->addLine(Line::fromArticle(Article::get(15)));
$order->addLine(Line::fromBarcode(new Barcode('2637485960')));

$orderId = $order->create();
```

Many examples are provided in the `docs/examples/` directory of this repository.


## Installation 

Using composer, it is just:
```bash
composer require atabix/swretail-api-client 
```

## Configuration

When using Laravel, the `config/swretail.php` is copied to your project config. See that file for details about the `.env` values you will need: 

```ini
SWRETAIL_ENDPOINT=
SWRETAIL_USERNAME=
SWRETAIL_PASSWORD=
```

----
