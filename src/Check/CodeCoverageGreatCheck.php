<?php

namespace SilverStripe\ModuleRatings\Check;

class CodeCoverageGreatCheck extends AbstractCodeCoverageCheck
{
    protected $threshold = 60;

    protected $points = 5;

    public function getKey()
    {
        return 'great_code_coverage';
    }

    public function run()
    {
        if ($this->getCoverage() >= $this->getThreshold()) {
            $this->setSuccessful(true);
        }
    }
}
