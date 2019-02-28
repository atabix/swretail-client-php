<?php

namespace Tests\Articles;

use SWRetail\Models\Article\Size;
use Tests\TestCase;

class SizesTest extends TestCase
{
    /**
     * @test
     * @dataProvider getSizesProvider
     *
     * @param array $sizesData
     */
    public function testGetStockFromSizes($sizesData)
    {
        $sizes = [];
        $totalStockValue = 0;
        foreach ($sizesData as $sizeValues) {
            $size = (new Size())->setMappedValues($sizeValues);
            $this->assertEquals($sizeValues['stock'], $size->getStock());
            
            $sizes[] = $size;
            $totalStockValue += $sizeValues['stock'];
        }

        $total = \array_reduce($sizes, function ($carry, $size) {
            $carry += $size->getStock();

            return $carry;
        }, 0);

        $this->assertEquals($totalStockValue, $total);
    }

    public function getSizesProvider()
    {
        return [
            'set-1' => [
                [
                    [
                        'position'       => 1,
                        'description'    => 'foo',
                        'stock'          => 3,
                        'salepricedelta' => '0.00',
                        'purpricedelta'  => '0.00',
                    ],
                    [
                        'position'       => 2,
                        'description'    => 'bar',
                        'stock'          => 2,
                        'salepricedelta' => '0.00',
                        'purpricedelta'  => '0.00',
                    ],
                ],
            ],
        ];
    }
}
