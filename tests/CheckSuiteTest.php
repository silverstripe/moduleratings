<?php

namespace SilverStripe\ModuleRatings\Tests;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check;
use SilverStripe\ModuleRatings\CheckSuite;

class CheckSuiteTest extends TestCase
{
    /**
     * @var CheckSuite
     */
    protected $checkSuite;

    protected function setUp()
    {
        parent::setUp();

        $this->checkSuite = new CheckSuite();
        $this->checkSuite->setChecks([
            new CheckSuiteTest\StubCheckFail(),
            new CheckSuiteTest\StubCheckPass(),
        ]);
    }

    /**
     * @expectedException Exception
     */
    public function testRunThrowsExceptionWhenNoChecksAreDefined()
    {
        $this->checkSuite->setChecks([]);
        $this->checkSuite->run();
    }

    public function testRun()
    {
        $this->checkSuite->run();

        $expected = [
            'fails' => [
                'description' => 'a stub that always fails',
                'maximum' => 5,
                'points' => 0,
            ],
            'passes' => [
                'description' => 'a stub that always passes',
                'maximum' => 5,
                'points' => 5,
            ],
        ];

        $this->assertSame(5, $this->checkSuite->getPoints());
        $this->assertEquals($expected, $this->checkSuite->getCheckDetails());
    }

    public function testRunWithDelegatedCallback()
    {
        $called = false;
        $callback = function (Check $check, callable $delegate) use (&$called) {
            $called = true;
            $delegate($check);
        };

        $this->checkSuite->run($callback);
        $this->assertTrue($called, 'Callback method was run');
        $this->assertSame(5, $this->checkSuite->getPoints(), 'Callback method delegated');
    }
}
