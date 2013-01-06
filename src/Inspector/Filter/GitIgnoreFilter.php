<?php

namespace Inspector\Filter;

class GitIgnoreFilter
{
    private $excludedFiles;

    public function __construct($gitignore_location = null)
    {
        $gitignore_location = ($gitignore_location ?: getcwd()).'/.gitignore';

        if (file_exists($gitignore_location)) {
            $gitignore = file($gitignore_location, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            if (false === $gitignore) {
                throw new \RunTimeException('Problems with loading the ".gitignore"');
            }
            $this->excludedFiles = $gitignore;
        } else {
            throw new \RunTimeException('.gitignore file not found');
        }
    }

    public function filter(\SplFileInfo $file)
    {
        foreach ($this->excludedFiles as $exclude) {
            if (preg_match($this->convertStringToPattern($exclude), $file->getRealPath())) {
                return false;
            }
        }

        return true;
    }

    private function convertStringToPattern($string)
    {
        $start = substr($string, 0, 1);
        $end = substr($string, -1);

        if ($start === $end || ('{' === $start && '}' === $end)) {
            // regex
            return $string;
        } else {
            return '/'.str_replace(array('/', '.', '*'), array('\/', '\.', '.*?'), $string).'/';
        }
    }
}
