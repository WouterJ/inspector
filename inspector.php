<?php

require_once __DIR__.'/vendor/autoload.php';

use Inspector\Provider;

/*-----------------------------------*\
    Configure container
\*-----------------------------------*/
$container = new Pimple();

// register listeners
Provider\FinderServiceProvider::register($container);
Provider\EventDispatcherServiceProvider::register($container);
Provider\InspectorServiceProvider::register($container);
Provider\ConsoleServiceProvider::register($container);


/*-----------------------------------*\
    Run console application
\*-----------------------------------*/
$application = $container['console.application'];
$application->run();
