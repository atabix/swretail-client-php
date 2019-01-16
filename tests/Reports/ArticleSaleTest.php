<?php

namespace Tests\Reports;

use SWRetail\Models\Article;
use SWRetail\Models\Report\ArticleSale;
use SWRetail\Models\Type\Percentage;
use SWRetail\Models\Type\Price;
use Tests\TestCase;

class ArticleSaleTest extends TestCase
{
    /**
     * @test
     * @dataProvider articleSalesRepsonseProvider
     *
     * @param int $articleId
     */
    public function testParseArticleSalesResponse($rawData)
    {
        $json = \json_decode($rawData);

        $list = [];
        foreach ($json as $itemData) {
            $item = new ArticleSale();
            $item->parseData($itemData);

            $this->assertInstanceOf(Percentage::class, $item->getTaxRate());
            $this->assertInstanceOf(Price::class, $item->getLineTotal());
            $this->assertIsInt($item->getArticleId());
            $this->assertIsString($item->getEmployee());

            $list[] = $item;
        }
    }

    public function articleSalesRepsonseProvider()
    {
        return [
            'by-article' => [
                'rawData' => '[
    {
        "article_id": 15,
        "size_pos": 2,
        "amount": 2,
        "linetotal": "130.0000",
        "discount": "9.90",
        "tax_percent": "21.00",
        "employee": "internet",
        "relation_code": "K100127",
        "cash_id": 0,
        "store_id": null,
        "receipt_number": 0,
        "article_link": "/swcloud/SWWService/article/15"
    },
    {
        "article_id": 15,
        "size_pos": 3,
        "amount": 2,
        "linetotal": "130.0000",
        "discount": "9.90",
        "tax_percent": "21.00",
        "employee": "internet",
        "relation_code": "K100127",
        "cash_id": 0,
        "store_id": null,
        "receipt_number": 0,
        "article_link": "/swcloud/SWWService/article/15"
    }
]',
            ],
        ];
    }
}
