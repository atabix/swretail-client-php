<?php 

// LIST WAREHOUSES 
$warehouses = Warehouse::getAll();
var_dump($warehouses);

$warehouse = $warehouses[0];

// LIST ARTICLES WITH CHANGE IN STOCK DURING THE LAST 1500 MINUTES.
$stockarticles = Article::stockChanged(1500)->get();
var_dump($stockarticles);

// LIST ARTICLES UPDATED DURING THE LAST 1500 MINUTES.
$allchanged = Article::allChanged(1500)->get();
var_dump($allchanged);


$article = Article::get(17);
$position = 2;

// GET THE STOCK OF AN ARTICLE POSITION AT A WAREHOUSE.
$stock = $article->stockAt($position, $warehouse)->get();
var_dump($stock);

// CHANGE THE STOCK OF AN ARTICLE POSITION AT A WAREHOUSE.
$stock = $article->stockAt($position, $warehouse)->set(35);
var_dump($stock);
