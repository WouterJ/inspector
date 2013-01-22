<?php

namespace Inspector\Exception;

class OptionNotProvidedException extends \RunTimeException
{
    public function __construct($option_name, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                'The "%s" option must be provided',
                $option_name
            ),
            $code,
            $previous
        );
    }
}
