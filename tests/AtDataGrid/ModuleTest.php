<?php

namespace AtDataGridTest;

use PHPUnit\Framework\TestCase;
use AtDataGrid\Module;

/**
 * @covers AtDataGrid\Module
 */
class ModuleTest extends TestCase
{
    public function testGetAutoloaderConfig()
    {
        $module = new Module();

        $this->assertTrue(is_array($module->getAutoloaderConfig()));
        $this->assertCount(1, $module->getAutoloaderConfig());
        $this->assertArrayHasKey('Zend\Loader\StandardAutoloader', $module->getAutoloaderConfig());
    }

    public function testGetConfig()
    {
        $module = new Module();

        $this->assertTrue(is_array($module->getConfig()));
    }
}