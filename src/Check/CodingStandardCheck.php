<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class CodingStandardCheck extends Check
{
    protected $points = 10;

    /**
     * The path to the PHPCS standards file
     *
     * @var string
     */
    protected $standardsFile = '';

    public function __construct()
    {
        $this->setStandardsFile(realpath(__DIR__) . '/CodingStandardCheck/phpcs.xml.dist');
    }

    public function getKey()
    {
        return 'coding_standards';
    }

    public function getDescription()
    {
        return 'The PHP code in this module passes the SilverStripe lint rules (mostly PSR-2)';
    }

    /**
     * Get PHP CodeSniffer and run it over the current module. Assigns a successful result if the codebase passes
     * the linting check with no errors.
     */
    public function run()
    {
        $standard = '--standard=' . escapeshellarg($this->getStandardsFile());
        $path = $this->getSuite()->getModuleRoot();

        $output = null;
        exec(
            'cd ' . dirname(__FILE__) . '/../../../../../ && vendor/bin/phpcs -q '
            . $standard . ' ' . $path,
            $ouput,
            $exitCode
        );
        if ($exitCode == 0) {
            $this->setSuccessful(true);
        }
    }

    public function setStandardsFile($standardsFile)
    {
        $this->standardsFile = $standardsFile;
        return $this;
    }

    public function getStandardsFile()
    {
        return $this->standardsFile;
    }
}
