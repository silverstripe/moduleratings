<?php

namespace SilverStripe\ModuleRatings\Check;

use Exception;
use SilverStripe\ModuleRatings\Check;

class CodingStandardCheck extends Check
{
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
        $arguments = [];
        if (file_exists($this->getSuite()->getModuleRoot() . '/phpcs.xml.dist')) {
            $includePaths = [];
            $ignorePaths = [];
            $moduleXml = file_get_contents($this->getSuite()->getModuleRoot() . '/phpcs.xml.dist');

            preg_match_all('/<exclude-pattern>(.*)<\/exclude-pattern>/', $moduleXml, $ignorePaths);

            if (count($ignorePaths) > 0 && count($ignorePaths[1]) > 0) {
                foreach($ignorePaths[1] as $ignorePath) {
                    $arguments[] = '--ignore="' . $ignorePath . '"';
                }
            }

            preg_match_all('/<file>(.*)<\/file>/', $moduleXml, $includePaths);

            if (count($includePaths) > 0 && count($includePaths[1]) > 0) {
                foreach($includePaths[1] as $includePath) {
                    $arguments[] = $this->getSuite()->getModuleRoot() . DIRECTORY_SEPARATOR . $includePath;
                }
            } else {
                $arguments[] = $this->getSuite()->getModuleRoot();
            }
        } else {
            $arguments[] = $this->getSuite()->getModuleRoot();
        }

        $standard = '--standard=' . escapeshellarg($this->getStandardsFile());

        $output = null;
        exec(
            'cd ' . $this->getProjectRoot() . ' && vendor/bin/phpcs -q '
            . $standard . ' ' . implode(' ', $arguments),
            $output,
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

    public function getProjectRoot()
    {
        $base = dirname(__FILE__);
        if (file_exists($base . '/../../../../../vendor/autoload.php')) {
            // Installed in the vendor folder
            return $base . '/../../../../../';
        } elseif (file_exists($base . '/../../vendor/autoload.php')) {
            // Installed as the project
            return $base . '/../../';
        }
        throw new Exception('Could not find the project root folder!');
    }
}
