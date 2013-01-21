<?php

namespace Inspector\Provider;

use Inspector\Exception;

/**
 * Registers services for the Symfony Finder component.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FinderServiceProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function register(\Pimple $container)
    {
        $container['finder.class'] = 'Symfony\Component\Finder\Finder';

        if (class_exists($container['finder.class'])) {
            $container['finder'] = function ($c) {
                return new $c['finder.class']();
            };
        } else {
            throw new Exception\Provider\ClassNotFoundException('Finder', $container['finder.class']);
        }
    }
}
