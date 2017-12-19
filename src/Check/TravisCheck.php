<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class TravisCheck extends Check
{
    protected $points = 10;

    public function getKey()
    {
        return 'travis_passing';
    }

    public function run()
    {
        $slug = $this->getSuite()->getRepositorySlug();
        $result = @file_get_contents('https://api.travis-ci.org/repositories/' . $slug . '.json');
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return;
        }

        // Not set up (404)
        if (isset($response['file']) && $response['file'] === 'not found') {
            return;
        }

        // Passing?
        if (isset($response['last_build_result']) && (int) $response['last_build_result'] === 0) {
            $this->setSuccessful(true);
        }
    }
}
