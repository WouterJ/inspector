<?php

namespace Inspector\Finder;

use Symfony\Component\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    /**
     * {@inheritDocs}
     *
     * @param callable $callable
     *
     * @return self
     */
    public function filter($callable)
    {
        parent::filter(function (\SplFileInfo $file) use ($callable) {
            return call_user_func_array($callable, array($file));
        });

        return $this;
    }
}
