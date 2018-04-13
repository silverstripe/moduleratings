<?php

namespace SilverStripe\ModuleRatings\Check;

class ReadmeCheck extends AbstractFileCheck
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
        $files = $this->getFinder()
            ->files()
            ->in($this->getSuite()->getModuleRoot())
            ->name('/readme(?:\.md|\.txt)?$/i');

        $this->setSuccessful(count($files) > 0);
    }
}
