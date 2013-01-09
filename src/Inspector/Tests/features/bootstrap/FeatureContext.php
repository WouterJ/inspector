<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Event\SuiteEvent;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $tmp_dir;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->tmp_dir = $tmp = isset($parameters['tmp_dir'])
            ? $parameters['tmp_dir']
            : sys_get_temp_dir()
        ;

        if (!file_exists($tmp.'/inspector')) {
            mkdir($tmp.'/inspector');
        }

        $this->tmp_dir .= '/inspector';
    }

    /**
     * @Given /^I am in a directory called "([^"]*)"$/
     */
    public function inDir($dir)
    {
        $this->dir = $dir;

        $dir = $this->tmp_dir.'/'.$dir;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        chdir($dir);
    }

    /**
     * @Given /^I have a file called "([^"]*)" which contains "([^"]*)"$/
     */
    public function withFile($file, $content)
    {
        file_put_contents($file, $content);
    }

    /**
     * @When /^I run "([^"]*)" with "([^"]*)"$/
     */
    public function runCommand($command, $options)
    {
        exec('php e:\wouter\web\wamp\www\Inspector\inspector.php '.$command.' '.$options, $output);

        $this->display = $output;
    }

    /**
     * @Then /^I should get:$/
     */
    public function getResult(PyStringNode $expected)
    {
        $tmp = substr($this->tmp_dir, 0, -1);
        $lines = array_slice($expected->getLines(), 2);
        $display = array_slice($this->display, 3);

        $normalize = function ($str) use ($tmp) {
            return str_replace('\\', '/', $str);
        };

        $i = 0;
        foreach ($lines as $line) {
            if ($normalize($line) !== $normalize($display[$i++])) {
                throw new Exception(
                    sprintf(
                        'Failed asserting that "%s" is equal to "%s"',
                        $normalize($line),
                        $normalize(current($display))
                    )
                );
            }
        }
    }
}
