<?php

namespace Tests\Articles;

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
