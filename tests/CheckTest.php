<?php

namespace SilverStripe\ModuleRatings\Tests;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check;

class CheckTest extends TestCase
{
    /**
     * @param bool $isSuccessful
     * @param int $expected
     * @dataProvider resultProvider
     */
    public function testGetResult($isSuccessful, $expected)
    {
        $check = $this->getMockBuilder(Check::class)
            ->getMockForAbstractClass();

        $check
            ->setPoints(10)
            ->setSuccessful($isSuccessful);

        $this->assertSame($expected, $check->getResult());
    }

    /**
     * @return array[]
     */
    public function resultProvider()
    {
        return [
            'succeeded' => [true, 10],
            'failed' => [false, 0],
        ];
    }
}
