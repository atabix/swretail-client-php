<?php 

// CREATE ARTICLE.

$article = new Article($articleNumber = 2104, $season = 0, $id = 17);
$article
    ->setCategory('Kleding', 'Dames', 'Jurken') // group. subgroup, subsubgroup
    ->setDescription('Groene Kerstjurk')
    ->setManufacturer('Peoples Clothing')
    ->setManufacturerNumber('PC665422')
    ->setSupplier('H&M')
    ->setAdditionalInfo('Lorem ipsum dolor sid amet.')
    ->setColor('Groen')
    // ->setSizeruler(946)
    ->setWeight(647.5)
    ->setFreefield1('Vrolijk Kerstfeest')
    ->setInWebshop(true)
    ->setHomepage(true);

$article->priceInfo()
    ->setBase(new Price(59.95))
    ->setPurchase('52.50')
    ->setWholesale('50.00')
    ->setTaxRate('21%')
    ->setDiscount('8.5%')
    ->setWeb(new Price(69.95))
    ->setWebDiscount(new Percentage(10.0));

// $article->metaInfo() // not documented

$article
    ->addSize(Size::barcode('21040001')->setName('XS')->setPosition(1))
    ->addSize(Size::barcode('21040002')->setName('S')->setPosition(2))
    ->addSize(Size::barcode('21040003')->setName('M')->setPosition(3))
    ->addSize(Size::barcode('21040004')->setName('L')->setPosition(4))
    ->addSize(Size::barcode('21040005')->setName('XL')->setPosition(5));

// - OR (instead of sizes) -
$article->addBarcode(new Barcode('21040001', 1));


$articleId = $article->create(); // Call API.

var_dump($articleId);
