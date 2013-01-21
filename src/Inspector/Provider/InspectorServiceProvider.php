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

        $container['inspector.filter.filters'] = new \ArrayObject();

        $container['inspector.filter.listener.filters'] = array();
        $container['inspector.filter.listener'] = function ($c) {
            return new \Inspector\Listener\FilterListener($c['inspector.filter.filters'], $c['inspector.filter.listener.filters']);
        };
        $container['inspector.filter.filters']['gitignore'] = function () {
            return new Filter\GitIgnoreFilter();
        };
        $container['inspector'] = function ($c) {
            $c['event_dispatcher']->addListener(\Inspector\InspectorEvents::FIND, array($c['inspector.filter.listener'], 'onFind'));
            

            return new $c['inspector.class']($c['finder'], $c['event_dispatcher']);
        };
    }
}
