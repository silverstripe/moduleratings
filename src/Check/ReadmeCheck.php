<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class ReadmeCheck extends Check
{
    public function getKey()
    {
        return 'has_readme';
    }

    public function getDescription()
    {
        return 'Has a readme file';
    }

    public function run()
    {
        $options = ['readme', 'README', 'readme.md', 'README.md', 'readme.txt', 'README.txt'];
        foreach ($options as $filename) {
            if (file_exists($this->getSuite()->getModuleRoot() . '/' . $filename)) {
                $this->setSuccessful(true);
                break;
            }
        }
    }
}
