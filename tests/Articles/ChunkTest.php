<?php

namespace Tests\Articles;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use SWRetail\Http\Response;
use SWRetail\Models\Article;
use SWRetail\Models\Article\Barcode;
use SWRetail\Models\Article\Chunks;
use Tests\TestCase;

class ChunkTest extends TestCase
{
    /**
     * @test
     * @dataProvider parseChunkResponsesProvider
     *
     * @param array[string] $rawResponses
     */
    public function testParseChunkResponses($rawResponses)
    {
        $chunks = Article::chunks();

        foreach ($rawResponses as $chunk => $responseData) {
            $response = new Response(new GuzzleResponse(200, [], $responseData));
            $response->parseJsonBody();

            $list = $chunks->handleChunkResponse($response);
            $this->assertIsArray($list);
            foreach ($list as $article) {
                $this->assertInstanceOf(Article::class, $article);
                $this->assertNotEmpty($article->getNumber());
                // echo $article->getNumber() .' | ' . $article->getDescription() . ' | ' . $article->priceInfo()->getBase() . "\n";
            }
        }
    }

    /**
     * @test
     * @dataProvider parseChunkResponsesProvider
     *
     * @param array[string] $rawResponses
     */
    public function testParseYieldChunks($rawResponses)
    {
        $mockChunks = $this->getMockBuilder(Chunks::class)
            ->setMethods(['get'])
            ->getMock();

        $mockChunks->method('get')->will(
            $this->returnCallback(function ($arg) use ($rawResponses, $mockChunks) {
                $responseData = $rawResponses[$arg];
                $response = new Response(new GuzzleResponse(200, [], $responseData));
                $response->parseJsonBody();

                return $mockChunks->handleChunkResponse($response);
            })
        );

        foreach ($mockChunks->yieldAll() as $id => $article) {
            $this->assertIsInt($id);
            $this->assertInstanceOf(Article::class, $article);
            $this->assertNotEmpty($article->getNumber());
            // echo " * $id | " . $article->getNumber() .' | ' . $article->getDescription() . ' | ' . $article->priceInfo()->getBase() . "\n";
        }
    }


    public function parseChunkResponsesProvider()
    {
        return [
            'set-1' => [
                'arg-1' => [
                    '0' => '{
    "0": {
        "article_id": 2,
        "article_inwebshop": 0,
        "article_number": 1000,
        "article_season": 0,
        "sizeruler": 936,
        "article_freefield2": null,
        "article_manufacturer": "Nike",
        "article_artfabr": "1234f12",
        "article_freefield1": null,
        "article_color": "Blauw",
        "article_subgroup": "Sport",
        "article_group": "Schoenen",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "Voetbalschoen",
        "article_basepurprice": "45.00",
        "article_baseprice": "99.95",
        "article_outlet": 0,
        "article_discount": "0",
        "article_memo": null,
        "article_supplier": null,
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "Voetbal",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "2Voetbalschoen",
                "image_file": "2Voetbalschoen.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/2Voetbalschoen.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": "35",
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 2,
                "description": "36",
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 3,
                "description": "37",
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 4,
                "description": "38",
                "stock": 2,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 5,
                "description": "39",
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 6,
                "description": "40",
                "stock": 6,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 7,
                "description": "41",
                "stock": 4,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 8,
                "description": "42",
                "stock": 4,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 9,
                "description": "43",
                "stock": 3,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001032"
            },
            {
                "position": 2,
                "barcode": "0000001033"
            },
            {
                "position": 3,
                "barcode": "0000001034"
            },
            {
                "position": 4,
                "barcode": "0000001035"
            },
            {
                "position": 5,
                "barcode": "0000001036"
            },
            {
                "position": 6,
                "barcode": "0000001037"
            },
            {
                "position": 7,
                "barcode": "0000001038"
            },
            {
                "position": 8,
                "barcode": "0000001039"
            },
            {
                "position": 9,
                "barcode": "0000001040"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "1": {
        "article_id": 3,
        "article_inwebshop": 0,
        "article_number": 1001,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Samsung",
        "article_artfabr": "UE32M5620",
        "article_freefield1": "",
        "article_color": "Zwart",
        "article_subgroup": "TV",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": "Met de Samsung 32M5620 heb je altijd toegang tot je favoriete films en series. Dankzij de smart tv functie worden apps, games en online entertainment overzichtelijk samengebracht. Zo schakel je snel tussen een YouTube video en een serie van Netflix. Het maakt niet uit via welke bron je kijkt, want de Full HD ondersteuning zorgt voor een scherpe weergave van HD content. De tv beschikt over 3 HDMI ingangen. Hierop sluit je eenvoudig meerdere HDMI apparaten tegelijkertijd aan. Denk aan je Blu-ray speler, spelcomputer of externe decoder. ",
        "article_description": "TV 32 inch",
        "article_basepurprice": "432.00",
        "article_baseprice": "495.00",
        "article_outlet": 0,
        "article_discount": "10%",
        "article_memo": null,
        "article_supplier": "Samsung BV",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "32 inch",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "3TV_32_inch",
                "image_file": "3TV_32_inch.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/3TV_32_inch.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 4,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001027"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "2": {
        "article_id": 4,
        "article_inwebshop": 0,
        "article_number": 1002,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Samsung",
        "article_artfabr": "UE40MU6100 ",
        "article_freefield1": null,
        "article_color": "Zwart",
        "article_subgroup": "TV",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "TV 40 inch",
        "article_basepurprice": "380.00",
        "article_baseprice": "549.00",
        "article_outlet": 0,
        "article_discount": "10%",
        "article_memo": null,
        "article_supplier": "Samsung BV",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "40 inch",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "4TV_40_inch",
                "image_file": "4TV_40_inch.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/4TV_40_inch.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 7,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001028"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "3": {
        "article_id": 5,
        "article_inwebshop": 0,
        "article_number": 1003,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Philips",
        "article_artfabr": "32PFS5362",
        "article_freefield1": null,
        "article_color": "Zwart",
        "article_subgroup": "TV",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "TV 32 inch",
        "article_basepurprice": "256.00",
        "article_baseprice": "349.00",
        "article_outlet": 0,
        "article_discount": "10%",
        "article_memo": null,
        "article_supplier": "Philips Eindhoven",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "32 inch",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "5TV_32_inch",
                "image_file": "5TV_32_inch.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/5TV_32_inch.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001029"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "4": {
        "article_id": 6,
        "article_inwebshop": 0,
        "article_number": 1004,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Philips",
        "article_artfabr": "40PHS4112",
        "article_freefield1": null,
        "article_color": "Zwart",
        "article_subgroup": "TV",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "tv 40 inch",
        "article_basepurprice": "179.00",
        "article_baseprice": "398.00",
        "article_outlet": 0,
        "article_discount": "10%",
        "article_memo": null,
        "article_supplier": "Philips Eindhoven",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "40 inch",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "6tv_40_inch",
                "image_file": "6tv_40_inch.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/6tv_40_inch.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 7,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001030"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "5": {
        "article_id": 7,
        "article_inwebshop": 0,
        "article_number": 1005,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Apple",
        "article_artfabr": "aip7128zw",
        "article_freefield1": null,
        "article_color": "Zwart",
        "article_subgroup": "Telefoon",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "Iphone 7 128gb",
        "article_basepurprice": "473.00",
        "article_baseprice": "699.00",
        "article_outlet": 0,
        "article_discount": "10",
        "article_memo": null,
        "article_supplier": "Apple Inc.",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "7Iphone_7_128gb",
                "image_file": "7Iphone_7_128gb.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/7Iphone_7_128gb.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 14,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001043"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "6": {
        "article_id": 8,
        "article_inwebshop": 0,
        "article_number": 1006,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Apple",
        "article_artfabr": "aip7128wi",
        "article_freefield1": null,
        "article_color": "Wit",
        "article_subgroup": "Telefoon",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "Iphone 7 128gb",
        "article_basepurprice": "473.00",
        "article_baseprice": "699.00",
        "article_outlet": 0,
        "article_discount": "10",
        "article_memo": null,
        "article_supplier": "Apple Inc.",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "8Iphone_7_128gb",
                "image_file": "8Iphone_7_128gb.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/8Iphone_7_128gb.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 15,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001044"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "7": {
        "article_id": 9,
        "article_inwebshop": 0,
        "article_number": 1007,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Apple",
        "article_artfabr": "aip764wi",
        "article_freefield1": null,
        "article_color": "Wit",
        "article_subgroup": "Telefoon",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "Iphone 7 64gb",
        "article_basepurprice": "473.00",
        "article_baseprice": "699.00",
        "article_outlet": 0,
        "article_discount": "10",
        "article_memo": null,
        "article_supplier": "Apple Inc.",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "9Iphone_7_64gb",
                "image_file": "9Iphone_7_64gb.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/9Iphone_7_64gb.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 22,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001045"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "8": {
        "article_id": 10,
        "article_inwebshop": 0,
        "article_number": 1008,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": "Apple",
        "article_artfabr": "aip764zw",
        "article_freefield1": null,
        "article_color": "Zwart",
        "article_subgroup": "Telefoon",
        "article_group": "Electronica",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "Iphone 7 64gb",
        "article_basepurprice": "473.00",
        "article_baseprice": "699.00",
        "article_outlet": 0,
        "article_discount": "10",
        "article_memo": null,
        "article_supplier": "Apple Inc.",
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "10Iphone_7_64gb",
                "image_file": "10Iphone_7_64gb.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/10Iphone_7_64gb.jpg"
            }
        ],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 17,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001042"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "9": {
        "article_id": 11,
        "article_inwebshop": 0,
        "article_number": 1009,
        "article_season": 0,
        "sizeruler": 946,
        "article_freefield2": null,
        "article_manufacturer": "Wam Denim",
        "article_artfabr": "75392",
        "article_freefield1": null,
        "article_color": "Blauw",
        "article_subgroup": "Heren",
        "article_group": "Kleding",
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": "Blouse",
        "article_basepurprice": "34.00",
        "article_baseprice": "69.95",
        "article_outlet": 0,
        "article_discount": "20",
        "article_memo": null,
        "article_supplier": null,
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": "Blouse",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [
            {
                "image_description": "11Blouse",
                "image_file": "11Blouse.jpg",
                "image_order": 1,
                "image_link": "/swcloud/SWWService/article_image/11Blouse.jpg"
            }
        ],
        "sizes": [
            {
                "position": 2,
                "description": "XS",
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 3,
                "description": "S",
                "stock": 5,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 4,
                "description": "M",
                "stock": 6,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 5,
                "description": "L",
                "stock": 4,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 6,
                "description": "XL",
                "stock": 3,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            },
            {
                "position": 7,
                "description": "XXL",
                "stock": 6,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 2,
                "barcode": "0000001046"
            },
            {
                "position": 3,
                "barcode": "0000001047"
            },
            {
                "position": 4,
                "barcode": "0000001048"
            },
            {
                "position": 5,
                "barcode": "0000001049"
            },
            {
                "position": 6,
                "barcode": "0000001050"
            },
            {
                "position": 7,
                "barcode": "0000001051"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "next_page": 1
}',
                    '1' => '{
    "0": {
        "article_id": 12,
        "article_inwebshop": 0,
        "article_number": 1010,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": null,
        "article_manufacturer": null,
        "article_artfabr": null,
        "article_freefield1": null,
        "article_color": null,
        "article_subgroup": null,
        "article_group": null,
        "article_price_web": null,
        "article_additional_info": null,
        "article_description": null,
        "article_basepurprice": "0.00",
        "article_baseprice": "0.00",
        "article_outlet": 0,
        "article_discount": "0",
        "article_memo": null,
        "article_supplier": null,
        "article_price_web_discount": "",
        "article_homepage": 0,
        "article_weight": "0.00",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "0.00",
        "article_subsubgroup": null,
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 0,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001031"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "1": {
        "article_id": 13,
        "article_inwebshop": 0,
        "article_number": 2100,
        "article_season": 0,
        "sizeruler": -3,
        "article_freefield2": "",
        "article_manufacturer": "Peoples Clothing",
        "article_artfabr": "D701440",
        "article_freefield1": "",
        "article_color": "Zwart",
        "article_subgroup": "Dames",
        "article_group": "Kleding",
        "article_price_web": "39.95",
        "article_additional_info": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut commodo quam eu mi viverra, sit amet euismod quam commodo. Nullam diam lectus, faucibus vitae elit quis, tincidunt ultrices turpis.",
        "article_description": "Sjieke zwarte damesrok",
        "article_basepurprice": "31.27",
        "article_baseprice": "39.95",
        "article_outlet": 0,
        "article_discount": "9.95",
        "article_memo": "in de Kerstcollectie",
        "article_supplier": "C&A",
        "article_price_web_discount": "9.95",
        "article_homepage": 0,
        "article_weight": "231.50",
        "article_metakeywords": null,
        "article_metadescription": null,
        "article_metatitle": null,
        "article_price_wholesale": "30.00",
        "article_subsubgroup": "Rokken",
        "article_taxrate": "21.00",
        "article_group_extra": [],
        "fields": [],
        "images": [],
        "sizes": [
            {
                "position": 1,
                "description": null,
                "stock": 0,
                "salepricedelta": "0.00",
                "purpricedelta": "0.00"
            }
        ],
        "barcodes": [
            {
                "position": 1,
                "barcode": "0000001053"
            }
        ],
        "article_crossell": [],
        "article_upsell": [],
        "article_actions": []
    },
    "next_page": -1
}',
                ],
            ],
        ];
    }
}
