<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\CodeCoverageGoodCheck;
use SilverStripe\ModuleRatings\Check\CodeCoverageGreatCheck;

class CodeCoverageGoodCheckTest extends TestCase
{
    /**
     * @covers CodeCoverageGoodCheck::run
     * @covers CodeCoverageGreatCheck::run
     *
     * @param float $coverage
     * @param bool $expected
     * @dataProvider checkProvider
     */
    public function testCheck($coverage, $expected)
    {
        $check = $this->getMockBuilder(CodeCoverageGoodCheck::class)
            ->setMethods(['getCoverage'])
            ->getMock();

        $check->expects($this->once())->method('getCoverage')->willReturn($coverage);

        $check->run();

        $this->assertSame($expected, $check->getSuccessful());
    }

    /**
     * @return array[]
     */
    public function checkProvider()
    {
        return [
            'low_coverage' => [10, false],
            'even_coverage' => [40, true],
            'higher_coverage' => [99, true],
        ];
    }
}
