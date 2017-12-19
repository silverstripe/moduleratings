<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class GitAttributesFileCheck extends Check
{
    protected $points = 2;

    public function getKey()
    {
        return 'has_gitattributes_file';
    }

    public function getDescription()
    {
        return 'Has a .gitattributes file';
    }

    public function run()
    {
        if (file_exists($this->getSuite()->getModuleRoot() . '/.gitattributes')) {
            $this->setSuccessful(true);
        }
    }
}
