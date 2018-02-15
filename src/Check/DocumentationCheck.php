<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class DocumentationCheck extends Check
{
    public function getKey()
    {
        return 'has_documentation';
    }

    public function getDescription()
    {
        return 'Has documentation';
    }

    public function run()
    {
        if (file_exists($this->getSuite()->getModuleRoot() . '/docs')) {
            $this->setSuccessful(true);
        }
    }
}
