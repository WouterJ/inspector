<?php

namespace Inspector\Exception;

class ClassNotFoundException extends \LogicException
{
    public function __construct($class_name, $message = 'The class "%s" does not exists', $package = false, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                $message.($package ? ', did you installed the package correctly?' : ''),
                $class_name
            ),
            $code,
            $previous
        );
    }
}
