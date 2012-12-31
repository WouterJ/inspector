<?php

namespace Inspector\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    private $container;

    public function __construct(\Pimple $container)
    {
        $this->container = $container;

        parent::__construct('Inspector', '1.0.0');

        $this->getHelperSet()->set(new Command\Helper\TableHelper());
    }

    public function getContainer()
    {
        return $this->container;
    }
}
