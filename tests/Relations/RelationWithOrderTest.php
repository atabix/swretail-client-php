<?php

namespace Tests\Relations;

use SWRetail\Models\Order;
use SWRetail\Models\Relation;
use Tests\TestCase;

class RelationWithOrderTest extends TestCase
{
    /**
     * @dataProvider oneRelationWithOrdersResponseProvider
     *
     * @param string Raw json data.
     */
    public function testParseOneRelationWithOrdersResponse($rawData)
    {
        $json = \json_decode($rawData);

        $relation = new Relation($json->relationtype, $json->relationcode);
        $relation->parseData($json);

        $this->assertIsArray($relation->orders());
        $this->assertEquals(\count($json->orders), \count($relation->orders()));

        foreach ($relation->orders() as $order) {
            $this->assertInstanceOf(Order::class, $order);
            $this->assertGreaterThan(1, \count($order->lines()));
        }
    }

    public function oneRelationWithOrdersResponseProvider()
    {
        return [
            'set-1' => [
                'rawData' => '{
    "lastname": "Bakker",
    "street": "Broodjesweg",
    "city": "Gisteveen",
    "zipcode": "4848BR",
    "phone1": "07611122233",
    "phone2": null,
    "country": "NL",
    "email": "banketbakker@example.com",
    "contact": "Bruin",
    "relationtype": 2,
    "housenumber": "2",
    "loyaltypoints": 0,
    "sex": "Mw",
    "birthdate": "1986-04-26",
    "barcode": null,
    "newsletter": 0,
    "firstname": "Ban-Ket",
    "relationcode": "K100127",
    "relationgroup": "Winkels",
    "external_id": "C24",
    "icp": 0,
    "orders": [
        {
            "invoicenumber": 0,
            "order_id": 352489,
            "swretail_state": null,
            "date": "2018-12-09",
            "time": "23:35",
            "inetnumber": "WO1001",
            "paymethod": "iDeal",
            "web_state": "paid",
            "remark": "Een test order",
            "linked_id": null,
            "shipper": "PostNL",
            "wholesale": false,
            "relation_code_ship": "K100127",
            "relation_code_invoice": "K100127",
            "order_lines": [
                {
                    "order_id": 352489,
                    "article_number": null,
                    "article_season": null,
                    "article_freefield2": null,
                    "article_freefield1": null,
                    "article_subgroup": "PostNL",
                    "article_group": null,
                    "article_price": "4.78",
                    "amount": 1,
                    "discount": "0.00",
                    "article_id": 0,
                    "size": null,
                    "article_description": "PostNL",
                    "linetotal": "4.7800",
                    "non_pickable": true
                },
                {
                    "order_id": 352489,
                    "article_number": 2101,
                    "article_season": 0,
                    "article_freefield2": "",
                    "article_freefield1": "Vrolijke Kerst",
                    "article_subgroup": "Dames",
                    "article_group": "Kleding",
                    "article_price": "69.95",
                    "amount": 1,
                    "discount": "4.95",
                    "article_id": 14,
                    "size": "40",
                    "article_description": "Foute Kerstjurk",
                    "linetotal": "65.0000",
                    "non_pickable": false
                },
                {
                    "order_id": 352489,
                    "article_number": 2102,
                    "article_season": 0,
                    "article_freefield2": "",
                    "article_freefield1": "Vrolijke Kerst",
                    "article_subgroup": "Dames",
                    "article_group": "Kleding",
                    "article_price": "69.95",
                    "amount": 2,
                    "discount": "9.90",
                    "article_id": 15,
                    "size": "40",
                    "article_description": "Foute Kerstjurk Rendier",
                    "linetotal": "130.0000",
                    "non_pickable": false
                }
            ]
        },
        {
            "invoicenumber": 0,
            "order_id": 352489,
            "swretail_state": null,
            "date": "2018-12-09",
            "time": "23:35",
            "inetnumber": "WO1001",
            "paymethod": "iDeal",
            "web_state": "paid",
            "remark": "Een test order",
            "linked_id": null,
            "shipper": "PostNL",
            "wholesale": false,
            "relation_code_ship": "K100127",
            "relation_code_invoice": "K100127",
            "order_lines": [
                {
                    "order_id": 352489,
                    "article_number": null,
                    "article_season": null,
                    "article_freefield2": null,
                    "article_freefield1": null,
                    "article_subgroup": "PostNL",
                    "article_group": null,
                    "article_price": "4.78",
                    "amount": 1,
                    "discount": "0.00",
                    "article_id": 0,
                    "size": null,
                    "article_description": "PostNL",
                    "linetotal": "4.7800",
                    "non_pickable": true
                },
                {
                    "order_id": 352489,
                    "article_number": 2101,
                    "article_season": 0,
                    "article_freefield2": "",
                    "article_freefield1": "Vrolijke Kerst",
                    "article_subgroup": "Dames",
                    "article_group": "Kleding",
                    "article_price": "69.95",
                    "amount": 1,
                    "discount": "4.95",
                    "article_id": 14,
                    "size": "40",
                    "article_description": "Foute Kerstjurk",
                    "linetotal": "65.0000",
                    "non_pickable": false
                },
                {
                    "order_id": 352489,
                    "article_number": 2102,
                    "article_season": 0,
                    "article_freefield2": "",
                    "article_freefield1": "Vrolijke Kerst",
                    "article_subgroup": "Dames",
                    "article_group": "Kleding",
                    "article_price": "69.95",
                    "amount": 2,
                    "discount": "9.90",
                    "article_id": 15,
                    "size": "40",
                    "article_description": "Foute Kerstjurk Rendier",
                    "linetotal": "130.0000",
                    "non_pickable": false
                }
            ]
        }
    ]
}',
            ],
        ];
    }
}
