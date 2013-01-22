<?php

namespace Inspector\Exception\Provider;

use Inspector\Exception

class ClassNotFoundException extends Exception\ClassNotFoundException
{
    public function __construct($service_name, $class_name, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            $class_name,
            sprintf(
                'Cannot register "%s" service, because the "%%s" class does not exists',
                $service_name
            ),
            true,
            $code,
            $previous
        );
    }
}
