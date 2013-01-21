<?php

namespace Inspector\Provider;

use Inspector\Exception;

/**
 * Registers the services for the EventDispatcher component.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class EventDispatcherServiceProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function register(\Pimple $container)
    {
        $container['event_dispatcher.class'] = 'Symfony\Component\EventDispatcher\EventDispatcher';

        if (class_exists($container['event_dispatcher.class'])) {
            $container['event_dispatcher'] = $container->share(function ($c) {
                return new $c['event_dispatcher.class']();
            });
        } else {
            throw new Exception\Provider\ClassNotFoundException('Event Dispatcher', $container['event_dispatcher.class']);
        }
    }
}
