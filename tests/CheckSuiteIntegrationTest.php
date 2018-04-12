<?php

namespace SilverStripe\ModuleRatings\Tests;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\CheckSuite;

/**
 * @group integration
 */
class CheckSuiteIntegrationTest extends TestCase
{
    /**
     * @var CheckSuite
     */
    protected $checkSuite;

    protected function setUp()
    {
        parent::setUp();

        $this->checkSuite = new CheckSuite();
        $this->checkSuite->setModuleRoot(dirname(__FILE__) . '/TestModule');
    }

    /**
     * Runs a test check suite over the "TestModule" test module directory. Note that at this point this doesn't
     * stub the results from API based checks, but rather just ignores them because no repository slug is provided.
     */
    public function testRun()
    {
        $this->checkSuite->run();

        $this->assertEquals(58, $this->checkSuite->getScore());
        $this->assertEquals(0, $this->checkSuite->getCheckDetails()['ci_passing']['points']);
    }
}
