<?php 

use SWRetail\Models\Report;

// All these report results have their items aggregated by Store/CashRegister combi.

$date = '2019-01-09';

// Get turnover report data for date.
$turnovers = Report::turnovers($date)->getAll();

// Get payment report data for date.
$payments = Report::payments($date)->getAll();

// Get article sales report data for date.
$sales = Report::articleSales($date)->getAll();

// Only reports for a specific article 
$article = 15; // article ID or an Article instance.
$sales = Report::articleSales($date)->article($article)->getAll();

// Only reports for a specific receipt number
$receipt = 1;
$sales = Report::articleSales($date)->receipt($receipt)->getAll();
