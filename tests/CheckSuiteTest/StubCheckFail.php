<?php

namespace SilverStripe\ModuleRatings\Tests\CheckSuiteTest;

use SilverStripe\ModuleRatings\Check;

class StubCheckFail extends Check
{
    protected $points = 5;

    public function getKey()
    {
        return 'fails';
    }

    public function getDescription()
    {
        return 'a stub that always fails';
    }

    public function run()
    {
        // no op
    }
}
