<?php

namespace Inspector\Provider;

use Inspector\Exception;
use Inspector\Console\Command;

/**
 * Registers services for the Monolog package.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class LoggingServiceProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function register(\Pimple $container)
    {
        $container['logger.class'] = 'Monolog\Logger';

        if (class_exists($container['logger.class'])) {
            self::registerLogger($container);
            self::registerHandlers($container);
        } else {
            throw new Exception\Provider\ClassNotFoundException('Logging', $container['logger.class']);
        }
    }

    public static function registerLogger(\Pimple $container)
    {
        $container['logger'] = $container->share(function ($c) {
            return new $c['logger.class']();
        });
    }

    public static function registerHandlers(\Pimple $container)
    {
        $container['logger'] = $container->extend('logger', function ($logger, $c) {
            $logger->pushHandler(new StreamHandler(getcwd().'/_inspector.log'));

            return $logger;
        });
    }
}
