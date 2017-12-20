<?php

namespace SilverStripe\ModuleRatings\Tests;

use PHPUnit\Framework\TestCase;
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
//            new CheckSuiteTest\StubCheckFail(),
//            new CheckSuiteTest\StubCheckPass(),
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
}
