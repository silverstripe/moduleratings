<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class LicenseCheck extends Check
{
    protected $points = 5;

    public function getKey()
    {
        return 'has_license';
    }

    public function run()
    {
        $options = ['license', 'LICENSE', 'license.md', 'LICENSE.md', 'license.txt', 'LICENSE.txt'];
        foreach ($options as $filename) {
            if (file_exists($this->getSuite()->getModuleRoot() . '/' . $filename)) {
                $this->setSuccessful(true);
                break;
            }
        }
    }
}
