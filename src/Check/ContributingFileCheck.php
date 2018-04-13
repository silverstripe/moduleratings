<?php

namespace SilverStripe\ModuleRatings\Check;

class ContributingFileCheck extends AbstractFileCheck
{
    public function getKey()
    {
        return 'has_contributing_file';
    }

    public function getDescription()
    {
        return 'Has a contributing guide file';
    }

    public function run()
    {
        $files = $this->getFinder()
            ->files()
            ->in($this->getSuite()->getModuleRoot())
            ->name('/contributing(?:\.md|\.txt)?$/i');

        $this->setSuccessful(count($files) > 0);
    }
}
