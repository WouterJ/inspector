<?php

namespace Inspector\Provider;

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
        $container['console.command'] = new \ArrayObject();

        $container['console.application.class'] = 'Inspector\Console\Application';
        $container['console.command']['inspector'] = function () {
            return new Command\InspectorCommand();
        };
        $container['console.application'] = $container->share(function ($c) {
            $app = new $c['console.application.class']($c);

            foreach ($c['console.command'] as $command) {
                $app->add($command());
            }

            return $app;
        });
    }
}
