<?php

namespace SilverStripe\ModuleRatings;

use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;

class CheckSuite
{
    /**
     * @var Check[]
     */
    protected $checks = [];

    /**
     * @var array
     */
    protected $checkDetails = [];

    /**
     * @var int
     */
    protected $points = 0;

    /**
     * The physical filepath to the module's code
     *
     * @var string
     */
    protected $moduleRoot;

    /**
     * @var string
     */
    protected $repositorySlug = '';

    public function run()
    {
        foreach ($this->getChecks() as $check) {
            /** @var Check $check */
            $this->processCheck($check);
        }
    }

    /**
     * Run the check and handle its results
     *
     * @param Check $check
     */
    protected function processCheck(Check $check)
    {
        $check->run();

        $this->addPoints($check->getResult());
        $this->addCheckDetail($check->getKey(), $check->getResult());
    }

    /**
     * Use the calculator class to get a relative score for the total number of points possible out of 100
     *
     * @return int
     */
    public function getScore()
    {
        return $this->getInjector()->get(Calculator::class)->calculate($this->getPoints());
    }

    /**
     * Get an Injector from SilverStripe 3 or 4
     *
     * @return object Injector instance
     */
    public function getInjector()
    {
        return class_exists('Injector') ? \Injector::inst() : \SilverStripe\Core\Injector\Injector::inst();
    }

    /**
     * Get the number of points for this module's rating
     *
     * @return int
     */
    public function getPoints()
    {
        return (int) $this->points;
    }

    /**
     * Set the number of points for this module's rating
     *
     * @param int $points
     * @return $this
     */
    public function setPoints($points)
    {
        $this->points = (int) $points;
        return $this;
    }

    /**
     * Add the number of points to this module's rating
     *
     * @param int $points
     * @return $this
     */
    public function addPoints($points)
    {
        $this->setPoints($this->getPoints() + (int) $points);
        return $this;
    }

    /**
     * Set the detailed result for the checks in this suite
     *
     * @param array $checkResults
     * @return $this
     */
    public function setCheckDetails(array $checkDetails)
    {
        $this->checkDetails = $checkDetails;
        return $this;
    }

    /**
     * Add a specific point result for a check in this suite
     *
     * @param string $key
     * @param int $points
     * @return $this
     */
    public function addCheckDetail($key, $points)
    {
        $this->checkDetails[$key] = (int) $points;
        return $this;
    }

    /**
     * Get the details result for the checks in this suite
     * @return array
     */
    public function getCheckDetails()
    {
        return $this->checkDetails;
    }

    /**
     * Get the registered check class instances
     *
     * @return Check[]
     */
    public function getChecks()
    {
        if (!$this->checks) {
            $this->buildChecks();
        }
        return $this->checks;
    }

    /**
     * Set the registered check class instances
     *
     * @param Check[] $checks
     * @return $this
     */
    public function setChecks(array $checks = [])
    {
        $this->checks = $checks;
        return $this;
    }

    /**
     * Get the registered check class names and try to instantiate and add them
     *
     * @throws InvalidArgumentException If a registered check class does not exist
     */
    protected function buildChecks()
    {
        $checkClasses = $this->getCheckClasses();
        foreach ($checkClasses as $checkClass) {
            if (!class_exists($checkClass)) {
                throw new InvalidArgumentException('Registered check class ' . $checkClass . ' not found!');
            }

            /** @var Check $check */
            $check = new $checkClass;
            $check->setSuite($this);

            $this->addCheck($check);
        }
    }

    /**
     * Load the config.yml file and get the check class names from it
     *
     * @return string[]
     */
    protected function getCheckClasses()
    {
        $classes = [];

        $config = Yaml::parseFile(dirname(__FILE__) . '/../config.yml');

        print_r($config);
        exit;
    }

    /**
     * Add a new check to the stack
     *
     * @param Check $check
     * @return $this
     */
    public function addCheck(Check $check)
    {
        $this->checks[] = $check;
        return $this;
    }

    /**
     * Set the path to the module's root folder that we're going to examine
     *
     * @param string $moduleRoot
     * @return $this
     */
    public function setModuleRoot($moduleRoot)
    {
        $this->moduleRoot = (string) rtrim($moduleRoot, '/');
        return $this;
    }

    /**
     * Get the path to the module's root folder
     *
     * @return string
     */
    public function getModuleRoot()
    {
        return $this->moduleRoot;
    }

    /**
     * Set the repository slug from/for the URL
     *
     * @param string $repositorySlug
     * @return $this
     */
    public function setRepositorySlug($repositorySlug)
    {
        $this->repositorySlug = (string) $repositorySlug;
        return $this;
    }

    /**
     * Get the repository slug for the URL
     *
     * @return string
     */
    public function getRepositorySlug()
    {
        return $this->repositorySlug;
    }
}
