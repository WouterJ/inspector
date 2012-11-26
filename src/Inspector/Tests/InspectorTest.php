<?php

namespace Inspector\Tests;

use Inspector\Inspector;
use Inspector\Iterator\Suspects;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @covers Inspector
 */
class InspectorTest extends \PHPUnit_Framework_TestCase
{
    protected $inspector;

    public function setUp()
    {
        $this->inspector = new Inspector(new Finder());
    }
    public function tearDown()
    {
        $this->inspector = null;
    }

    public function testReturnsSuspects()
    {
        $this->assertInstanceOf('Inspector\Iterator\Suspects', $this->inspector->inspect(__DIR__.'/stubs', 'hello'));
    }

    public function testReturnSuspectsEvenIfNothingIsFound()
    {
        $this->assertInstanceOf('Inspector\Iterator\Suspects', $this->inspector->inspect(__DIR__.'/stubs', 'hello'));
    }

    /**
     * Inspect a directory and look for a string in a file.
     *
     * @covers Inspector::inspect
     */
    public function testStringInspection()
    {
        $suspects = $this->inspector->inspect(__DIR__.'/stubs', 'foo');

        $this->assertSuspectsContain(array('test4.xml'), $suspects);
    }

    /**
     * Inspect a directory and look for a Regular Expression to match a file.
     *
     * @covers Inspector::inspect
     */
    public function testRegexInspection()
    {
        $suspects = $this->inspector->inspect(__DIR__.'/stubs', '/ba(r|z)/');

        $this->assertSuspectsContain(array(
            'test1.txt',
            'test2.php',
            'test4.xml',
        ), $suspects);
    }

    /**
     * Asserts that files are marked as suspect
     *
     * @param array $files
     * @param array $suspects
     */
    protected function assertSuspectsContain(array $files, Suspects $suspects)
    {
        // only show relative path
        $suspects = $suspects->getArrayCopy();
        $suspects = array_map(function ($file) {
            return $file->getRelativePathname();
        }, $suspects);

        foreach ($files as $file) {
            $this->assertContains($file, $suspects, sprintf('"%s" is a suspect', $file));
        }
    }
}
