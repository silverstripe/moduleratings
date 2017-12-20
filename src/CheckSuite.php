<?php

namespace SilverStripe\ModuleRatings;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class CheckSuite
{
    /**
     * @var Check[]|null
     */
    protected $checks = null;

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

    /**
     * Runs the check suite and processes the result of each
     *
     * @param callable $checkCallback If provided, will be called before each individual check is run, and will be
     *                                passed the Check instance as the first argument, and a delegatable callback
     *                                as the second argument for the consumer to control where the progressCheck()
     *                                method is called in their logic
     * @throws Exception              If no checks are defined
     */
    public function run(callable $checkCallback = null)
    {
        if (!$this->getChecks()) {
            throw new Exception('No checks have been defined! Please set some in config.yml.');
        }
        foreach ($this->getChecks() as $check) {
            /** @var Check $check */
            if (is_callable($checkCallback)) {
                $checkCallback($check, function () use ($check) {
                    $this->processCheck($check);
                });
            } else {
                $this->processCheck($check);
            }
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
        $this->addCheckDetail(
            $check->getKey(),
            [
                'description' => $check->getDescription(),
                'points' => $check->getResult(),
                'maximum' => $check->getPoints(),
            ]
        );
    }

    /**
     * Use the calculator class to get a relative score for the total number of points possible out of 100
     *
     * @return int
     */
    public function getScore()
    {
        return Calculator::getInstance()->calculate($this->getPoints());
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
     * Add some metrics (description, points, etc) result for a check in this suite
     *
     * @param string $key
     * @param array $metrics
     * @return $this
     */
    public function addCheckDetail($key, array $metrics)
    {
        $this->checkDetails[$key] = $metrics;
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
        if ($this->checks === null) {
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
        $config = Yaml::parse(file_get_contents(dirname(__FILE__) . '/../config.yml'));
        if (empty($config[self::class]['checks'])) {
            return [];
        }
        return (array) $config[self::class]['checks'];
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
