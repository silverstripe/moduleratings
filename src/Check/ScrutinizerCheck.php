<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class ScrutinizerCheck extends Check
{
    const THRESHOLD = 6.5;

    protected $points = 10;

    public function getKey()
    {
        return 'good_scrutinizer_score';
    }

    public function run()
    {
        $slug = $this->getSuite()->getRepositorySlug();
        $result = @file_get_contents('https://scrutinizer-ci.com/api/repositories/g/' . $slug);
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return;
        }

        // Not set up (404)
        if (!isset($response['applications']['master'])) {
            return;
        }

        $rating = $response['applications']['master']['index']['_embedded']['project']['metric_values']['scrutinizer.quality'];
        if ($rating >= self::THRESHOLD) {
            $this->setSuccessful(true);
        }
    }
}
