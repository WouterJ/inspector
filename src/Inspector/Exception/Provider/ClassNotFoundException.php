<?php

namespace Inspector\Exception\Provider;

class ClassNotFoundException extends \BadMethodCallException
{
    public function __construct($service_name, $class_name, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                'Cannot register "%s" service, because the "%s" class is not loaded',
                $service_name,
                $class_name
            ),
            $code,
            $previous
        );
    }
}
