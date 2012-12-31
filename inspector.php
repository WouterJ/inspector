<?php

require_once __DIR__.'/vendor/autoload.php';

/*-----------------------------------*\
    Configure container
\*-----------------------------------*/
$container = new Pimple();

$container['finder.class'] = 'Symfony\Component\Finder\Finder';
$container['finder'] = function ($c) {
    return new $c['finder.class']();
};

$container['event_dispatcher.class'] = 'Symfony\Component\EventDispatcher\EventDispatcher';
$container['event_dispatcher'] = $container->share(function ($c) {
    return new $c['event_dispatcher.class']();
});

$container['inspector.class'] = 'Inspector\Inspector';
$container['inspector'] = function ($c) {
    return new $c['inspector.class']($c['finder'], $c['event_dispatcher']);
};

$container['console.command'] = array(
    'inspector' => function () {
        return new Inspector\Console\Command\InspectorCommand();
    },
);

$container['console.application.class'] = 'Inspector\Console\Application';
$container['console.application'] = $container->share(function ($c) {
    $app = new $c['console.application.class']($c);

    foreach ($c['console.command'] as $command) {
        $app->add($command());
    }

    return $app;
});

/*-----------------------------------*\
    Run console application
\*-----------------------------------*/
$application = $container['console.application'];
$application->run();
