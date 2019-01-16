<?php

namespace Tests\Articles;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use SWRetail\Http\Response;
use SWRetail\Models\Article;
use SWRetail\Models\Article\Barcode;
use SWRetail\Models\Article\Category;
use SWRetail\Models\Article\PriceInfo;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    /**
     * @test
     * @dataProvider getArticleProvider
     *
     * @param int $articleId
     */
    public function testGetArticle($articleId)
    {
        $article = Article::get($articleId);

        $this->assertInstanceof(Article::class, $article);

        $this->assertEquals($articleId, $article->getId());
    }

    public function getArticleProvider()
    {
        return [
            [38],
        ];
    }

    /**
     * @test
     * @dataProvider parseArticleResponseProvider
     *
     * @param string $rawData Raw body response (json)
     */
    public function testParseArticleResponse($rawData)
    {
        $json = \json_decode($rawData);

        $response = new Response(new GuzzleResponse(200, [], $rawData));
        $response->parseJsonBody();

        $data = $response->json;
        $this->assertEquals($json, $data);
        
        $article = new Article($data->article_number, $data->article_season);
        $article->parseData($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals($json->article_number, $article->getNumber());
        $this->assertEquals($json->article_baseprice, $article->priceInfo()->getBase());

    }

    public function parseArticleResponseProvider()
    {
        return [
            'set-1' => [
                'arg-1' => '{
    "article_id": 38,
    "article_inwebshop": 1,
    "article_number": 57576,
    "article_season": 0,
    "sizeruler": -3,
    "article_freefield2": null,
    "article_manufacturer": "Masterlight",
    "article_artfabr": "2890-37-176-4-DW",
    "article_freefield1": null,
    "article_color": "Nikkel",
    "article_subgroup": "Hanglampen",
    "article_group": "Verlichting",
    "article_price_web": "288.00",
    "article_additional_info": "",
    "article_description": "",
    "article_basepurprice": "103.18",
    "article_baseprice": "288.00",
    "article_outlet": 0,
    "article_discount": "0",
    "article_memo": null,
    "article_supplier": "Masterlight",
    "article_price_web_discount": "0.00",
    "article_homepage": 0,
    "article_weight": "0.00",
    "article_metakeywords": null,
    "article_metadescription": null,
    "article_metatitle": null,
    "article_price_wholesale": "0.00",
    "article_subsubgroup": "Kroonluchters",
    "article_taxrate": "21.00",
    "article_group_extra": [],
    "fields": {
        "dimbaar": "",
        "inclusief_dimmer": "",
        "stijl": "",
        "inclusief_lichtbron": "",
        "materiaal": ""
    },
    "images": [],
    "sizes": [
        {
            "position": 1,
            "description": null,
            "stock": -3,
            "salepricedelta": "0.00",
            "purpricedelta": "0.00"
        }
    ],
    "barcodes": [
        {
            "position": 1,
            "barcode": "0000001079"
        },
        {
            "position": 1,
            "barcode": "57576"
        }
    ],
    "article_crossell": [],
    "article_upsell": [],
    "article_actions": []
}',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider buildNewArticleProvider
     */
    public function testBuildNewArticle($data)
    {
        $article = new Article($data['articleNumber'], $data['season']);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals($data['articleNumber'], $article->getNumber());

        $this->assertInstanceof(PriceInfo::class, $article->priceInfo());

        $article->setCategory($data['category']['main'], $data['category']['sub'], $data['category']['subsub']);

        $this->assertInstanceOf(Category::class, $article->getCategory());

        $article->setDescription($data['description']);
        $article->setAdditionalInfo($data['additionalInfo']);
        $article->setColor($data['color']);
        $article->setWeight($data['weight']);
        $article->setManufacturer($data['manufacturer'])
            ->setManufacturerNumber($data['manufacturerNumber']);
        $article->setInWebshop($data['inWebshop']);

        $article->priceInfo()
            ->setBase($data['prices']['base'])
            ->setPurchase($data['prices']['purchase'])
            ->setWholesale($data['prices']['wholesale'])
            ->setTaxRate($data['prices']['taxRate'])
            ->setWeb($data['prices']['web']);

        foreach ($data['barcodes'] as $position => $barcode) {
            $article->addBarcode(new Barcode($barcode, $position));
        }

        // Get data just before api call.
        $requestData = $article->toApiRequest();

        $this->assertIsArray($requestData);
        $this->assertArrayHasKey('article_number', $requestData);
        $this->assertEquals($data['articleNumber'], $requestData['article_number']);

        $this->assertEquals($data['category']['sub'], $requestData['article_subgroup']);
        $this->assertEquals($data['prices']['wholesale'], $requestData['article_price_wholesale']);

        $this->assertIsArray($requestData['barcodes']);
        $this->assertEquals($data['barcodes'][1], $requestData['barcodes'][0]['barcode']);
    }

    public function buildNewArticleProvider()
    {
        return [
            'test-1' => [
                [
                    'articleNumber' => '99901',
                    'season'        => 0,
                    'category'      => [
                        'main'   => 'Verlichting',
                        'sub'    => 'Woonkamer',
                        'subsub' => 'Spotjes',
                    ],
                    'description'        => 'Zwarte muurspotjes',
                    'additionalInfo'     => 'Sfeervolle spotjes voor de woonkamer',
                    'color'              => 'Zwart',
                    'weight'             => 35,
                    'manufacturer'       => 'Philips',
                    'manufacturerNumber' => 'PHSPOT8801',
                    'inWebshop'          => true,
                    'prices'             => [
                        'base'      => '20.00',
                        'purchase'  => '20.00',
                        'wholesale' => '17.50',
                        'web'       => '20.00',
                        'taxRate'   => '21%',
                    ],
                    'barcodes' => [
                        1 => '21040001',
                    ],
                ],
            ],
            // 'test-2' => [
            //     [
            //         //
            //     ],
            // ],
        ];
    }
}
