<?php

namespace SilverStripe\ModuleRatings;

abstract class Check
{
    /**
     * The number of points given when this check is successful
     *
     * @var int
     */
    protected $points = 0;

    /**
     * Whether this check was successful when it was run
     *
     * @var bool
     */
    protected $successful = false;

    /**
     * The controlling class responsible for running all checks
     *
     * @var CheckSuite
     */
    protected $suite;

    /**
     * Get the check "key", which is used for referencing this check in code
     *
     * @return string
     */
    abstract public function getKey();

    /**
     * Run the check logic, set the successful result at the ened
     *
     * @return void
     */
    abstract public function run();

    /**
     * Get the resulting number of points that has been assigned to the module for this check, depending
     * on whether it was successful or not.
     *
     * @return int
     */
    public function getResult()
    {
        if (!$this->getSuccessful()) {
            return 0;
        }
        return $this->getPoints();
    }

    public function setSuite(CheckSuite $suite)
    {
        $this->suite = $suite;
        return $this;
    }

    public function getSuite()
    {
        return $this->suite;
    }

    public function setPoints($points)
    {
        $this->points = (int) $points;
        return $this;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setSuccessful($result)
    {
        $this->successful = (bool) $result;
        return $this;
    }

    public function getSuccessful()
    {
        return $this->successful;
    }
}
