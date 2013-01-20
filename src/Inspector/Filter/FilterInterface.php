<?php

namespace Inspector\Filter;

/**
 * Interface used for custom build in filters.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface FilterInterface
{
    /**
     * Filters a file list.
     *
     * @param \SplFileInfo $file The current file
     *
     * @return boolean
     */
    public function filter(\SplFileInfo $file);
}
