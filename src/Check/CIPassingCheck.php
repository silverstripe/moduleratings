<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;

class CIPassingCheck extends Check
{
    /**
     * HTTP client request options
     *
     * @var array
     */
    protected $requestOptions = [
        'headers' => ['Accept' => 'application/json'],
    ];

    public function getKey()
    {
        return 'ci_passing';
    }

    public function getDescription()
    {
        return 'Has Travis CI or CircleCI configured and the last build passed successfully (requires slug)';
    }

    public function run()
    {
        // This check requires a repository slug to be provided
        $slug = $this->getSuite()->getRepositorySlug();
        if (!$slug) {
            return;
        }

        $result = $this->checkTravisBuild($slug) || $this->checkCircleCiBuild($slug);
        $this->setSuccessful((bool) $result);
    }

    /**
     * Uses the Travis API to check whether the latest CI build passed
     *
     * @param string $slug
     * @return bool
     */
    protected function checkTravisBuild($slug)
    {
        $result = $this->getRequestClient()
            ->get('https://api.travis-ci.org/repositories/' . $slug . '.json')
            ->getBody();
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return false;
        }

        // Not set up (404)
        if (isset($response['file']) && $response['file'] === 'not found') {
            return false;
        }

        // Passing?
        if (isset($response['last_build_result']) && (int) $response['last_build_result'] === 0) {
            return true;
        }
        return false;
    }

    /**
     * Uses the CircleCI API to check whether the latest CI build passed
     *
     * @param string $slug
     * @return bool
     */
    protected function checkCircleCiBuild($slug)
    {
        $result = $this->getRequestClient()
            ->get('https://circleci.com/api/v1.1/project/github/' . $slug, $this->requestOptions)
            ->getBody();
        $response = json_decode($result, true);

        // Fetch failure
        if (!$response) {
            return false;
        }

        // Not set up (404), e.g. {"message": "Project not found"}
        if (isset($response['message'])) {
            return false;
        }

        // Latest build passing?
        if (isset($response[0]['failed']) && (bool) $response[0]['failed'] === false) {
            return true;
        }
        return false;
    }
}
