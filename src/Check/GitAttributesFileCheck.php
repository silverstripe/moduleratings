<?php

namespace SilverStripe\ModuleRatings\Check;

class GitAttributesFileCheck extends AbstractFileCheck
{
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
        $files = $this->getFinder()
            ->files()
            ->ignoreDotFiles(false)
            ->in($this->getSuite()->getModuleRoot())
            ->name('.gitattributes');

        $this->setSuccessful(count($files) > 0);
    }
}
