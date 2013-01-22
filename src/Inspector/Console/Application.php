<?php

namespace Inspector\Console;

use Symfony\Component\Console\Application as BaseApplication;

/**
 * The Console Application.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Application extends BaseApplication
{
    /**
     * @var \Pimple
     */
    private $container;

    /**
     * {@inheritDocs}
     *
     * @param \Pimple $container
     */
    public function __construct(\Pimple $container)
    {
        $this->container = $container;

        parent::__construct('Inspector', '1.0.0-BETA1');

        $this->getHelperSet()->set(new Command\Helper\TableHelper());
    }

    /**
     * @return \Pimple
     */
    public function getContainer()
    {
        return $this->container;
    }
}
