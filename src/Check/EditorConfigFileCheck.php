<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class EditorConfigFileCheck extends Check
{
    protected $points = 5;

    public function getKey()
    {
        return 'has_editorconfig_file';
    }

    public function getDescription()
    {
        return 'Has a .editorconfig file';
    }

    public function run()
    {
        if (file_exists($this->getSuite()->getModuleRoot() . '/.editorconfig')) {
            $this->setSuccessful(true);
        }
    }
}
