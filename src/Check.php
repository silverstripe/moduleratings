<?php

namespace SilverStripe\ModuleRatings;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

abstract class Check
{
    /**
     * The number of points given when this check is successful. This is normally loaded in via configuration.
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
     * Utility class used for making HTTP requests
     *
     * @var ClientInterface
     */
    protected $requestClient;

    /**
     * @param ClientInterface $requestFactory
     */
    public function __construct(ClientInterface $requestClient = null)
    {
        if (!$requestClient) {
            $requestClient = new Client;
        }
        $this->setRequestClient($requestClient);
    }

    /**
     * Get the check "key", which is used for referencing this check in code
     *
     * @return string
     */
    abstract public function getKey();

    /**
     * Get the check description, which is used for humans
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Run the check logic, set the successful result at the end
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

    /**
     * @param CheckSuite $suite
     * @return $this
     */
    public function setSuite(CheckSuite $suite)
    {
        $this->suite = $suite;
        return $this;
    }

    /**
     * @return CheckSuite
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * Set the number of points that a successful check gives
     * @param int $points
     * @return $this
     */
    public function setPoints($points)
    {
        $this->points = (int) $points;
        return $this;
    }

    /**
     * The number of points that a successful check gives
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set whether the check successfully passed
     *
     * @param bool $result
     * @return $this
     */
    public function setSuccessful($result)
    {
        $this->successful = (bool) $result;
        return $this;
    }

    /**
     * Whether the check successfully passed
     * @return bool
     */
    public function getSuccessful()
    {
        return $this->successful;
    }

    /**
     * Set the HTTP request client
     *
     * @param ClientInterface $client
     * @return $this
     */
    public function setRequestClient(ClientInterface $client)
    {
        $this->requestClient = $client;
        return $this;
    }

    /**
     * Get the HTTP request client
     *
     * @return ClientInterface
     */
    public function getRequestClient()
    {
        return $this->requestClient;
    }
}
