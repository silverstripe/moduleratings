<?php

namespace SilverStripe\ModuleRatings\Check;

class DocumentationCheck extends AbstractFileCheck
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
        $files = $this->getFinder()
            ->directories()
            ->in($this->getSuite()->getModuleRoot())
            ->name('docs');

        $this->setSuccessful(count($files) > 0);
    }
}
