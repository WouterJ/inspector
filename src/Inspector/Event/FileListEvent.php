<?php

namespace Inspector\Event;

use Symfony\Component\Finder\Finder;

use Zend\EventManager\Event;

/**
 * This Event is used when @link{InspectorEvents::FIND} is dispatched.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FileListEvent extends Event
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @param Finder $finder
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }
}
