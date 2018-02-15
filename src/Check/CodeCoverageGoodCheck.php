<?php

namespace SilverStripe\ModuleRatings\Check;

class CodeCoverageGoodCheck extends AbstractCodeCoverageCheck
{
    protected $threshold = 40;

    public function getKey()
    {
        return 'good_code_coverage';
    }

    public function getDescription()
    {
        return 'Has a "good" level of code coverage (greater than ' . $this->threshold . '%, requires slug)';
    }

    public function run()
    {
        if ($this->getCoverage() >= $this->getThreshold()) {
            $this->setSuccessful(true);
        }
    }
}
