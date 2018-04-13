<?php

namespace SilverStripe\ModuleRatings\Check;

use SilverStripe\ModuleRatings\Check;
use Symfony\Component\Finder\Finder;

/**
 * Provides a base class for checks that rely on filesystem checks to extend, giving access to a
 * Symfony finder component
 */
abstract class AbstractFileCheck extends Check
{
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * Get the file finder component
     *
     * @return Finder
     */
    public function getFinder()
    {
        if (!$this->finder) {
            $this->finder = new Finder();
        }
        return $this->finder;
    }

    /**
     * Set the finder component to use
     *
     * @param Finder $finder
     * @return $this
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
        return $this;
    }
}
