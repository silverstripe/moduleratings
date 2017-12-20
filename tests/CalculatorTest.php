<?php

namespace SilverStripe\ModuleRatings\Tests;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Calculator;

class CalculatorTest extends TestCase
{
    public function testGetInstance()
    {
        $instanceA = Calculator::getInstance();
        $instanceB = Calculator::getInstance();

        $this->assertSame($instanceA, $instanceB);
    }

    public function testCalculate()
    {
        $mock = $this->getMockBuilder(Calculator::class)
            ->setMethods(['getMaxPoints'])
            ->getMock();

        $mock->expects($this->exactly(3))->method('getMaxPoints')->willReturn(140);

        $this->assertEquals(70, $mock->calculate(98));
        $this->assertEquals(0, $mock->calculate(0));
        $this->assertEquals(100, $mock->calculate(139));
    }
}
