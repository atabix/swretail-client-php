<?php

namespace SWRetail\Models;

use SWRetail\Models\Report\ArticleSale;
use SWRetail\Models\Report\Payment;
use SWRetail\Models\Report\Turnover;

class Report extends Model
{
    public static function turnovers($date) : Turnover
    {
        return (new Turnover())->date($date);
    }

    public static function payments($date) : Payment
    {
        return (new Payment())->date($date);
    }

    public static function articleSales($date) : ArticleSale
    {
        return (new ArticleSale())->date($date);
    }
}
