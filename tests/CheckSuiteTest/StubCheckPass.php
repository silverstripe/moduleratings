<?php

namespace SilverStripe\ModuleRatings\Tests\CheckSuiteTest;

use SilverStripe\ModuleRatings\Check;

class StubCheckPass extends Check
{
    protected $points = 5;

    public function getKey()
    {
        return 'passes';
    }

    public function getDescription()
    {
        return 'a stub that always passes';
    }

    public function run()
    {
        $this->setSuccessful(true);
    }
}
