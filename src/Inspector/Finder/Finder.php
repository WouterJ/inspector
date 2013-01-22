<?php

namespace Inspector\Finder;

use Symfony\Component\Finder\Finder as BaseFinder;

/**
 * The Finder that accepts PHP callables as a filter.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Finder extends BaseFinder
{
    /**
     * {@inheritDoc}
     *
     * @param callable $callable
     *
     * @return self
     *
     * @throws \InvalidArgumentException When the $callable isn't a callable
     */
    public function filter($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('The filter should be a PHP callable');
        }

        parent::filter(function (\SplFileInfo $file) use ($callable) {
            return call_user_func_array($callable, array($file));
        });

        return $this;
    }
}
