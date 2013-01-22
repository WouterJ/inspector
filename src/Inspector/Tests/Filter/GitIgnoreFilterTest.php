<?php

namespace Inspector\Tests\Filter;

use Inspector\Filter\GitIgnoreFilter;

class GitIgnoreFilterTest extends \PHPUnit_Framework_TestCase
{
    protected $filter;

    public function setUp()
    {
        $this->filter = new GitIgnoreFilter(realpath(__DIR__.'/../stubs'));
    }
    public function tearDown()
    {
        $this->filter = null;
    }

    /**
     * @expectedException RunTimeException
     * @expectedExceptionMessage The .gitignore file is not found in does-not-exists/.gitignore
     */
    public function testWrongGitDirectory()
    {
        new GitIgnoreFilter('does-not-exists');
    }

    /**
     * @dataProvider getIgnoreData
     */
    public function testIgnore($filename, $ignored)
    {
        $file = $this->getMockBuilder('SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
        $file
            ->expects($this->any())
            ->method('getRealPath')
            ->will($this->returnValue($filename))
        ;

        $this->assertEquals(!$ignored, $this->filter->filter($file));
    }

    public function getIgnoreData()
    {
        return array(
            // files
            array('file.txt', true),
            array('foo/bar/ipsum/file.txt', true),

            array('file.php', false),

            // dirs
            array('foodir/file.php', true),
            array('baz/foodir/ipsum/file.php', true),
            array('barf/file.php', true),
            array('barbaz/file.php', true),

            array('baz/file.php', false),
        );
    }
}
