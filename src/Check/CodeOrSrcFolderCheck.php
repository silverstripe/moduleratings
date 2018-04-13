<?php

namespace SilverStripe\ModuleRatings\Check;

class CodeOrSrcFolderCheck extends AbstractFileCheck
{
    public function getKey()
    {
        return 'has_code_or_src_folder';
    }

    public function getDescription()
    {
        return 'Has source code in either a "code" or a "src" folder';
    }

    public function run()
    {
        $files = $this->getFinder()
            ->directories()
            ->in($this->getSuite()->getModuleRoot())
            ->name('/code|src$/');

        $this->setSuccessful(count($files) === 1);
    }
}
