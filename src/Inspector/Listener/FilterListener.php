<?php

namespace Inspector\Listener;

use Inspector\Event\FileListEvent;
use Inspector\Filter\FilterInterface;

class FilterListener
{
    private $availableFilters;
    private $filters;

    /**
     * @param \Traversable $filters
     */
    public function __construct($availableFilters, $filters)
    {
        $this->setAvailableFilters($availableFilters);
        $this->setFilters($filters);
    }

    public function onFind(FileListEvent $event)
    {
        $filters = $this->getAvailableFilters();
        $choosenFilters = $this->getFilters();
        $finder = $event->getFinder();

        foreach ($filters as $name => $filter) {
            try {
                if (!in_array($name, $choosenFilters)) {
                    throw new \RuntimeException(sprintf('Filter "%s" is not choosen', $name));
                }

                if (is_callable($filter)) {
                    $filter = call_user_func($filter);
                } elseif (isset($filters[$filter])) {
                    $filter = call_user_func($filters[$filter]);
                } elseif (class_exists($filter)) {
                    $filter = new $filter();
                } else {
                    throw new \InvalidArgumentException('Filter must be a callable which returns a filter class, a build-in filter name or a filter class name.');
                }

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
    private function setAvailableFilters(\Traversable $filters)
    {
        $this->availableFilters = $filters;
    }

    /**
     * @return \Traversable
     */
    public function getAvailableFilters()
    {
        return $this->availableFilters;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
