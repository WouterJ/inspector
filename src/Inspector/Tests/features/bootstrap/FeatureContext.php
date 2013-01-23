<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Event;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $tmp;
    private static $cliDir;
    private $fs;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->tmp = (isset($parameters['tmp_dir'])
            ? $parameters['tmp_dir']
            : sys_get_temp_dir()
            ).'/inspector';

        $this->fs = new Filesystem();
    }

    /**
     * @AfterScenario
     */
    public function clearDir(Event\ScenarioEvent $event)
    {
        $fs = new Filesystem();
        $fs->remove($this->tmp);
    }

    /**
     * @Given /^I am in a directory called "([^"]*)"$/
     */
    public function inDir($dir)
    {
        if (null === self::$cliDir) {
            self::$cliDir = getcwd();
        }
        $this->dir = $dir;

        $dir = $this->tmp.'/'.$dir;
        $this->fs->mkdir($dir);
        
        chdir($dir);
    }

    /**
     * @Given /^I have a directory called "([^"]*)"$/
     */
    public function withDir($dir)
    {
        $this->fs->mkdir($dir);
    }

    /**
     * @Given /^I have a file called "([^"]*)" which contains "([^"]*)"$/
     */
    public function withFile($file, $content)
    {
        $content = str_replace('\n', PHP_EOL, $content);

        file_put_contents($file, $content);
    }

    /**
     * @Given /^I have a file called "([^"]*)" in "([^"]*)" which contains "([^"]*)"$/
     */
    public function withFileInDir($file, $dir, $content)
    {
        file_put_contents($dir.'/'.$file, $content);
    }

    /**
     * @When /^I run "([^"]*)" with "([^"]*)"$/
     */
    public function runCommand($command, $options)
    {
        exec('php "'.self::$cliDir.'/inspector.php" '.$command.' '.$options, $output);

        $this->display = $output;
    }

    /**
     * @Then /^I should get:$/
     */
    public function getResult(PyStringNode $expected)
    {
        $tmp = substr($this->tmp, 0, -1);

        if (!function_exists('normalize')) {
            function normalize($str) {
                return str_replace('\\', '/', $str);
            };
        }

        // expected
        $lines = array_slice($expected->getLines(), 2);
        $expectedFiles = array_map(function ($line) {
            // remove everything before the filepath
            return normalize(trim(preg_replace('/\d\s*/', '', $line)));
        }, $lines);

        // actual
        $display = array_slice($this->display, 3);
        $actualFiles = array_map(function ($line) {
            // remove everything before the filepath
            return normalize(trim(preg_replace('/\d\s*/', '', $line)));
        }, $display);

        // compare
        if ((0 === count($lines)) && 0 !== count($display)) {
            throw new \Exception('Failed asserting that there are no suspects');
        }

        foreach ($expectedFiles as $expected) {
            if (false !== ($key = array_search($expected, $actualFiles))) {
                unset($actualFiles[$key]);
            } else {
                throw new \Exception(
                    sprintf(
                        'Failed asserting that file "%s" is a suspect',
                        $expected
                    )
                );
            }
        }

        if (0 !== count($actualFiles)) {
            throw new \Exception(
                sprintf(
                    'The file(s) "%s" are suspects too',
                    implode('", "', $actualFiles)
                )
            );
        }
    }
}
