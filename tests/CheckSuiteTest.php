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

    public function testGetSetAddPoints()
    {
        $this->assertSame(0, $this->checkSuite->getPoints());

        $this->checkSuite->setPoints(100);
        $this->assertSame(100, $this->checkSuite->getPoints());

        $this->checkSuite->addPoints(5);
        $this->assertSame(105, $this->checkSuite->getPoints());
    }

    public function testGetSetAddCheckDetails()
    {
        $this->assertSame([], $this->checkSuite->getCheckDetails());

        $this->checkSuite->setCheckDetails([
            'some_check' => ['foo' => 'bar'],
        ]);
        $this->assertSame(['foo' => 'bar'], $this->checkSuite->getCheckDetails()['some_check']);

        $this->checkSuite->addCheckDetail('another_check', ['metric' => 10]);
        $this->assertSame(['metric' => 10], $this->checkSuite->getCheckDetail('another_check'));
    }

    /**
     * @expectedException Exception
     */
    public function testGetCheckDetailThrowsExceptionOnUnknownCheck()
    {
        $this->checkSuite->getCheckDetail('some_check_that_doesnt_exist');
    }

    public function testGetSetAddChecks()
    {
        $this->checkSuite->setChecks([]);
        $this->assertEmpty($this->checkSuite->getChecks());

        $this->checkSuite->addCheck(new CheckSuiteTest\StubCheckFail());
        $this->assertContainsOnlyInstancesOf(Check::class, $this->checkSuite->getChecks());
    }

    public function testSetModuleRoot()
    {
        $this->checkSuite->setModuleRoot('foo/bar');
        $this->assertSame('foo/bar', $this->checkSuite->getModuleRoot());

        $this->checkSuite->setModuleRoot('foo/bar/');
        $this->assertSame('foo/bar', $this->checkSuite->getModuleRoot());
    }
}
