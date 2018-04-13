<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

abstract class AbstractCodeCoverageCheck extends Check
{
    /**
     * Percent threshold for the coverage to be assessed at a certain level, e.g. good or great
     * @var int
     */
    protected $threshold = 0;

    public function getCoverage()
    {
        // This check requires a repository slug to be provided
        $slug = $this->getSuite()->getRepositorySlug();
        if (!$slug) {
            return 0;
        }

        // Priority: codecov.io
        $coverage = $this->getCodecovCoverage();
        if ($coverage === false) {
            // Fallback: scrutinizer-ci
            $coverage = $this->getScrutinizerCoverage();
        }
        return $coverage;
    }

    /**
     * Gets test coverage level from Codecov.io, returns false if not found or missing
     *
     * @return bool|float|int
     */
    public function getCodecovCoverage()
    {
        $slug = $this->getSuite()->getRepositorySlug();
        // Note: assume everyone uses the master branch
        $result = $this->getRequestClient()
            ->get('https://codecov.io/api/gh/' . $slug . '/branch/master')
            ->getBody();
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return false;
        }

        // Not set up (404)
        if (isset($response['meta']['status']) && (int) $response['meta']['status'] !== 200) {
            return false;
        }

        // Get coverage result
        if (isset($response['commit']['totals']['c'])) {
            return $response['commit']['totals']['c'];
        }

        return 0;
    }

    /**
     * Gets test coverage level from Scrutinizer, returns false if not found or missing
     *
     * @return bool|float|int
     */
    public function getScrutinizerCoverage()
    {
        $slug = $this->getSuite()->getRepositorySlug();
        // Note: assume everyone uses the master branch
        $result = $this->getRequestClient()
            ->get('https://scrutinizer-ci.com/api/repositories/g/' . $slug)
            ->getBody();
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return false;
        }

        // Not set up (404)
        if (!isset($response['applications']['master'])) {
            return false;
        }

        // Get coverage result
        $metrics = $response['applications']['master']['index']['_embedded']['project']['metric_values'];
        if (isset($metrics['scrutinizer.test_coverage'])) {
            return $metrics['scrutinizer.test_coverage'] * 100;
        }

        return 0;
    }

    /**
     * Get the threshold for measuring code coverage
     *
     * @return int
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * Set the threshold for measuring code coverage
     *
     * @param int $threshold
     * @return $this
     */
    public function setThreshold($threshold)
    {
        $this->threshold = $threshold;
        return $this;
    }
}
