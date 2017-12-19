<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class CodeOfConductFileCheck extends Check
{
    protected $points = 2;

    public function getKey()
    {
        return 'has_code_of_conduct_file';
    }

    public function getDescription()
    {
        return 'Has a code of conduct file';
    }

    public function run()
    {
        $options = [
            'code-of-conduct',
            'code_of_conduct',
            'codeofconduct',
        ];
        $extensions = ['', '.txt', '.md'];

        foreach ($options as $filename) {
            foreach ($extensions as $extension) {
                if (file_exists($this->getSuite()->getModuleRoot() . '/' . $filename . $extension)
                    || file_exists($this->getSuite()->getModuleRoot() . '/' . strtoupper($filename) . $extension)
                ) {
                    $this->setSuccessful(true);
                    break;
                }
            }
        }
    }
}
