<?php

namespace Tests\System;

use SWRetail\Models\Warehouse;
use Tests\TestCase;

class WarehousesTest extends TestCase
{
    
    /**
     * @test
     */
    public function testGetAll()
    {
        $warehouses = Warehouse::getAll();

        $this->assertIsArray($warehouses);
        $warehouse = \reset($warehouses);

        $this->assertInstanceOf(Warehouse::class, $warehouse);
    }
}
