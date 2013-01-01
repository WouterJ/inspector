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
     * @param string $directory The absolute path to a directory
     * @param string $needle    The string or Regular Expression to match with the content
     *
     * @return Suspects The selected files
     */
    public function inspect($directory, $needle)
    {
        // list files
        $this->getFinder()
            ->files()
            ->name('*')
            ->contains($needle)
            ->in($directory)
        ;

        $event = new FileListEvent();
        $event->setFinder($this->getFinder());

        $this->getDispatcher()->dispatch(InspectorEvents::FIND, $event);

        // mark files as suspect
        $suspects = new Suspects();

        foreach ($this->getFinder() as $file) {
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
