<?php

namespace Inspector;

use Inspector\InspectorEvents;
use Inspector\Iterator\Suspects;
use Inspector\Event;

use Symfony\Component\Finder\Finder;

use Zend\EventManager\EventManagerInterface;

/**
 * The heart of the Inspector application.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Inspector
{
    /**
     * @param Finder
     */
    private $finder;

    /**
     * @param EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param Finder                $finder
     * @param EventManagerInterface $dispatcher
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

        $event = new Event\FileListEvent();
        $event->setName(InspectorEvents::FIND);
        $event->setTarget($this);
        $event->setFinder($finder);

        $this->getDispatcher()->trigger($event);

        // mark files as suspect
        $suspects = new Suspects();

        foreach ($finder as $file) {
            $suspects->append($file);
        }

        $event = new Event\SuspectEvent();
        $event->setName(InspectorEvents::MARK);
        $event->setTarget($this);
        $event->setSuspects($suspects);

        $this->getDispatcher()->trigger($event);

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
     * @param EventManagerInterface $dispatcher
     */
    public function setDispatcher(EventManagerInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
