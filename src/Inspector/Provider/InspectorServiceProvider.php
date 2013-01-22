<?php

namespace Inspector\Provider;

use Inspector\Filter;

/**
 * Registers the services for the Inspector.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class InspectorServiceProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function register(\Pimple $container)
    {
        $container['inspector.class'] = 'Inspector\Inspector';

        self::registerListener($container);
        self::registerInspector($container);
    }

    public static function registerListener(\Pimple $container)
    {
        $container['inspector.filter_listener'] = function ($c) {
            return new \Inspector\Listener\FilterListener();
        };

        self::registerFilters($container);

        // register listener
        $container['event_dispatcher']->attach(
            \Inspector\InspectorEvents::FIND,
            array($container['inspector.filter_listener'], 'onFind')
        );
    }

    public static function registerFilters(\Pimple $container)
    {
        $container['inspector.filter_listener'] = $container->extend('inspector.filter_listener', function ($listener, $c) {
            $listener->addAvailableFilter('gitignore', new Filter\GitIgnoreFilter());

            return $listener;
        });
    }

    public static function registerInspector(\Pimple $container)
    {
        $container['inspector'] = function ($c) {
            return new $c['inspector.class']($c['finder'], $c['event_dispatcher']);
        };
    }
}
