<?php

namespace SilverStripe\ModuleRatings\Check;

class CodeCoverageGoodCheck extends AbstractCodeCoverageCheck
{
    protected $threshold = 40;

    protected $points = 5;

    public function getKey()
    {
        return 'good_code_coverage';
    }

    public function run()
    {
        if ($this->getCoverage() >= $this->getThreshold()) {
            $this->setSuccessful(true);
        }
    }
}
