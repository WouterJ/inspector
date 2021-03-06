<?php

namespace Inspector\Filter;

use Inspector\Util\MatchUtil;

/**
 * Filter that searches for a `.gitignore` file and ignores
 * the files that match the patterns in there.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class GitIgnoreFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $excludedFiles = array();

    /**
     * Searches for a gitignore file.
     *
     * @param string $gitignore_location Optional The location of the gitignore file
     *
     * @throws \RunTimeException When the `.gitignore` file isn't found
     * @throws \RunTimeException When we can't read the `.gitignore` file
     */
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
            throw new \RunTimeException(
                sprintf('The .gitignore file is not found in %s', $gitignore_location)
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function filter(\SplFileInfo $file)
    {
        foreach ($this->excludedFiles as $exclude) {
            if (preg_match($this->convertStringToPattern($exclude), $file->getRealPath())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function convertStringToPattern($string)
    {
        if (MatchUtil::isRegex($string)) {
            return $string;
        } else {
            return MatchUtil::convertGlob($string);
        }
    }
}
