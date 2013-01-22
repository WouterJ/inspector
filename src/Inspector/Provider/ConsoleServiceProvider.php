<?php

namespace Inspector\Provider;

use Inspector\Exception;
use Inspector\Console\Command;

/**
 * Registers services for the Console component.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class ConsoleServiceProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function register(\Pimple $container)
    {
        $container['console.application.class'] = 'Inspector\Console\Application';
        if (class_exists($container['console.application.class'])) {
            self::registerApplication($container);
        } else {
            throw new Exception\Provider\ClassNotFoundException('Console', $container['console.application.class']);
        }
    }

    public static function registerApplication(\Pimple $container)
    {
        $container['console.application'] = $container->share(function ($c) {
            $app = new $c['console.application.class']($c);

            foreach ($c['console.command'] as $command) {
                $app->add($command());
            }

            return $app;
        });

        $container['console.command'] = new \ArrayObject();
        self::registerCommands($container);
    }

    public static function registerCommands(\Pimple $container)
    {
        $container['console.command']['inspector'] = function () {
            return new Command\InspectorCommand();
        };
    }
}
