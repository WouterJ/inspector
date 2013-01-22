<?php

namespace Inspector\Listener;

use Inspector\Event\FileListEvent;
use Inspector\Filter\FilterInterface;
use Inspector\Exception;

/**
 * A class that handles build-in filters.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FilterListener
{
    private $availableFilters = array();
    private $filters = array();

    /**
     * Dispatching method.
     *
     * @param FileListEvent $event
     */
    public function onFind(FileListEvent $event)
    {
        $filters = $this->getAvailableFilters();
        $choosenFilters = $this->getFilters();

        foreach ($filters as $name => $filter) {
            try {
                $this->registerFilter($event->getFinder(), $name, $filter);
            } catch (Exception\Filter\ContinueException $e) {
                if (in_array($name, $choosenFilters)) {
                    throw $e;
                }

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
     * @throws Exception\Filter\ContinueException      When the filter wasn't choosen
     * @throws Exception\Filter\InvaildFilterException When the filter is wrong
     * @throws Exception\Filter\InvaildFilterException When the filter doesn't implement FilterInterface
     */
    private function registerFilter($finder, $name, $filter)
    {
        $filters = $this->getAvailableFilters();
        $choosenFilters = $this->getFilters();

        if (!in_array($name, $choosenFilters)) {
            throw new Exception\Filter\ContinueException(sprintf('Filter "%s" is not choosen', $name));
        }

        if (is_callable($filter)) {
            $filter = call_user_func($filter);
        } elseif (isset($filters[$filter])) {
            $filter = call_user_func($filters[$filter]);
        } elseif (class_exists($filter)) {
            $filter = new $filter();
        } else {
            throw new Exception\Filter\InvalidFilterException(array(
                'a callable which returns a filter class',
                'a build-in filter name',
            ));
        }

        if (!$filter instanceof FilterInterface) {
            throw new Exception\Filter\InvalidFilterException('an instance of Inspector\Filter\FilterInterface');
        }

        $finder->filter(function (\SplFileInfo $file) use ($filter) {
            return $filter->filter($file);
        });
    }

    public function setAvailableFilters(array $filters)
    {
        $this->availableFilters = $filters;
    }

    /**
     * @param string   $name
     * @param callable $filter
     */
    public function addAvailableFilter($name, $filter)
    {
        $filters = $this->getAvailableFilters();

        if (isset($filters[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Filter "%s" does already exists"', $name)
            );
        }

        if (!is_callable($filter)) {
            throw new \InvalidArgumentException(
                sprintf("The filter must be a callable, %s given", gettype($filter))
            );
        }

        $filters[$name] = $filter;

        $this->setAvailableFilters($filters);
    }

    /**
     * @return array
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
