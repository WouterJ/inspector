<?php

namespace Inspector\Provider;

/**
 * Used by every service provider to ensure it can register a service
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface ProviderInterface
{
    /**
     * Registers the services.
     *
     * @param \Pimple $container
     */
    public static function register(\Pimple $container);
}
