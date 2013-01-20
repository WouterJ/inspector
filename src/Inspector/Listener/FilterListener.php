<?php

namespace Inspector\Listener;

use Inspector\Event\FileListEvent;
use Inspector\Filter\FilterInterface;

/**
 * A class that handles build-in filters.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FilterListener
{
    /**
     * @var \Traversable
     */
    private $availableFilters;

    /**
     * @var array
     */
    private $filters;

    /**
     * @param \Traversable $availableFilters
     * @param array        $filters
     */
    public function __construct($availableFilters, $filters)
    {
        $this->setAvailableFilters($availableFilters);
        $this->setFilters($filters);
    }

    /**
     * Dispatching method.
     *
     * @param FileListEvent $event
     */
    public function onFind(FileListEvent $event)
    {
        $filters = $this->getAvailableFilters();

        foreach ($filters as $name => $filter) {
            try {
                $this->registerFilter($event->getFinder(), $name, $filter);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Registers the filter.
     *
     * @param Finder $finder
     * @param string $name   The filter's name
     * @param string $filter The filter
     *
     * @throws \RunTimeException         When the filter wasn't choosen
     * @throws \InvalidArgumentException When the filter is wrong
     * @throws \LogicException           When the filter doesn't implement FilterInterface
     */
    private function registerFilter($finder, $name, $filter)
    {
        $filters = $this->getAvailableFilters();
        $choosenFilters = $this->getFilters();

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
            throw new \InvalidArgumentException('Filter must be a callable which returns a filter class, a build-in filter name.');
        }

        if (!$filter instanceof FilterInterface) {
            throw new \LogicException(
                sprintf(
                    'The filter must be an instance of Inspector\Filter\FilterInterface',
                    get_class($filter)
                )
            );
        }

        $finder->filter(function (\SplFileInfo $file) use ($filter) {
            return $filter->filter($file);
        });
    }

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
