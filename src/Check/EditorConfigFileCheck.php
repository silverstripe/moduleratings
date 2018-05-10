<?php

namespace SilverStripe\ModuleRatings\Check;

class EditorConfigFileCheck extends AbstractFileCheck
{
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
        $files = $this->getFinder()
            ->files()
            ->ignoreDotFiles(false)
            ->in($this->getSuite()->getModuleRoot())
            ->name('.editorconfig');

        $this->setSuccessful(count($files) > 0);
    }
}
