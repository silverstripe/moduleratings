<?php

namespace SilverStripe\ModuleRatings\Check;

use Exception;
use SilverStripe\ModuleRatings\Check;

class ScrutinizerCheck extends Check
{
    const THRESHOLD = 6.5;

    public function getKey()
    {
        return 'good_scrutinizer_score';
    }

    public function getDescription()
    {
        return 'Has Scrutinizer CI configured and a "good" score (greater than '
            . self::THRESHOLD . '/10, requires slug)';
    }

    public function run()
    {
        // This check requires a repository slug to be provided
        $slug = $this->getSuite()->getRepositorySlug();
        if (!$slug) {
            return;
        }

        try {
            $result = $this->getRequestClient()
                ->get('https://scrutinizer-ci.com/api/repositories/g/' . $slug)
                ->getBody();
        } catch (Exception $ex) {
            if ($logger = $this->getSuite()->getLogger()) {
                $logger->debug($ex->getMessage());
            }
            $result = '';
        }
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return;
        }

        foreach(['master', 'main'] as $branch) {
            // Not set up (404)
            if (!isset($response['applications'][$branch]['index']['_embedded']['project']['metric_values'])) {
                continue;
            }

            $metrics = $response['applications'][$branch]['index']['_embedded']['project']['metric_values'];
            if ($metrics['scrutinizer.quality'] >= self::THRESHOLD) {
                $this->setSuccessful(true);

                return;
            }
        }
    }
}
