<?php

namespace SilverStripe\ModuleRatings\Check;

class LicenseCheck extends AbstractFileCheck
{
    public function getKey()
    {
        return 'has_license';
    }

    public function getDescription()
    {
        return 'Has a license file';
    }

    public function run()
    {
        $files = $this->getFinder()
            ->files()
            ->in($this->getSuite()->getModuleRoot())
            ->name('/license(?:\.md|\.txt)?$/i');

        $this->setSuccessful(count($files) > 0);
    }
}
