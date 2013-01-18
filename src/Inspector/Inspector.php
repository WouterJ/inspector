<?php

namespace Inspector;

use Inspector\InspectorEvents;
use Inspector\Iterator\Suspects;
use Inspector\Event\SuspectEvent;
use Inspector\Event\FileListEvent;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Inspector
{
    private $finder;
    private $dispatcher;

    /**
     * @param Finder                   $finder
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($finder, $dispatcher)
    {
        $this->setFinder($finder);
        $this->setDispatcher($dispatcher);
    }

    /**
     * Inspects a directory and selects files that match a needle.
     *
     * @param string       $directory          The absolute path to a directory
     * @param string       $needle             The string or Regular Expression to match with the content
     * @param string|array $filter    Optional Defines a pattern(s) to exclude files
     *
     * @return Suspects The selected files
     */
    public function inspect($directory, $needle, $filter = null)
    {
        // list files
        $finder = $this->getFinder()
            ->files()
            ->name('*')
            ->contains($needle)
            ->in($directory)
        ;

        if (null !== $filter) {
            if (is_array($filter)) {
                foreach ($filter as $f) {
                    $finder->notName($f);
                }
            } else {
                $finder->notName($filter);
            }
        }

        $event = new FileListEvent();
        $event->setFinder($finder);

        $this->getDispatcher()->dispatch(InspectorEvents::FIND, $event);

        // mark files as suspect
        $suspects = new Suspects();

        foreach ($finder as $file) {
            $suspects->append($file);
        }

        $event = new SuspectEvent();
        $event->setSuspects($suspects);

        $this->getDispatcher()->dispatch(InspectorEvents::MARK, $event);

        return $suspects;
    }

    /**
     * @return Finder|null
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * @param Finder $finder
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
