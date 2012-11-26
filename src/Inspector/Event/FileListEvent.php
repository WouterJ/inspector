<?php

namespace Inspector\Event;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\Event;

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
