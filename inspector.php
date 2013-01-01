<?php

require_once __DIR__.'/vendor/autoload.php';

/*-----------------------------------*\
    Configure container
\*-----------------------------------*/
$container = new Pimple();

// finder
$container['finder.class'] = 'Symfony\Component\Finder\Finder';
$container['finder'] = function ($c) {
    return new $c['finder.class']();
};

// event dispatcher
$container['event_dispatcher.class'] = 'Symfony\Component\EventDispatcher\EventDispatcher';
$container['event_dispatcher'] = $container->share(function ($c) {
    return new $c['event_dispatcher.class']();
});

// inspector
$container['inspector.class'] = 'Inspector\Inspector';

$container['inspector.filter.filters'] = new ArrayObject();
$container['inspector.filter.filters']['gitignore'] = function () {
    return new Inspector\Filter\GitIgnoreFilter();
};
$container['inspector.filter.listener'] = function ($c) {
    return new Inspector\Listener\FilterListener($c['inspector.filter.filters']);
};

$container['inspector'] = function ($c) {
    $c['event_dispatcher']->addListener(Inspector\InspectorEvents::FIND, array($c['inspector.filter.listener'], 'onFind'));
    

    return new $c['inspector.class']($c['finder'], $c['event_dispatcher']);
};

// console
$container['console.command'] = new ArrayObject();
$container['console.command']['inspector'] = function () {
    return new Inspector\Console\Command\InspectorCommand();
};

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
