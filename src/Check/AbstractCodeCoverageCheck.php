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

    public function getCodecovCoverage()
    {
        $slug = $this->getSuite()->getRepositorySlug();
        // Note: assume everyone uses the master branch
        $result = @file_get_contents('https://codecov.io/api/gh/' . $slug . '/branch/master');
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

    public function getScrutinizerCoverage()
    {
        // todo
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
