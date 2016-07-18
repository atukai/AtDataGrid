<?php

namespace AtDataGridTest;

use AtDataGrid\DataGrid;
use PHPUnit\Framework\TestCase;

/**
 * @covers AtDataGrid\DataGrid
 */
class DataGridTest extends TestCase
{
    public function testGridCreation()
    {
        $datasourceMock = $this->createMock('AtDataGrid\DataSource\ZendDb\TableGateway');
        $grid = new DataGrid($datasourceMock);

        $this->assertInstanceOf(\Countable::class, $grid);
        $this->assertInstanceOf(\IteratorAggregate::class, $grid);
        $this->assertInstanceOf(\ArrayAccess::class, $grid);
    }
}