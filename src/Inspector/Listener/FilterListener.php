<?php

namespace Inspector\Listener;

use Inspector\Event\FileListEvent;
use Inspector\Filter\FilterInterface;

class FilterListener
{
    private $filters;

    /**
     * @param \Traversable $filters
     */
    public function __construct($filters)
    {
        $this->setFilters($filters);
    }

    public function onFind(FileListEvent $event)
    {
        $filters = $this->getFilters();
        $finder = $event->getFinder();

        foreach ($filters as $filter) {
            try {
                $filter = $filter();
                if (!$filter instanceof FilterInterface) {
                    throw new \LogicException();
                }

                $finder->filter(function (\SplFileInfo $file) use ($filter) {
                    return $filter->filter($file);
                });
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * @param \Traversable $filters
     */
    private function setFilters(\Traversable $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \Traversable
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
