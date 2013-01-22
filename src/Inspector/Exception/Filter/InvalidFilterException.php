<?php

namespace Inspector\Exception\Filter;

class InvalidFilterException extends ContinueException
{
    /**
     * {@inheritDoc}
     *
     * @param string|array $allowedType The allowed type(s)
     */
    public function __construct($allowedType, $code = 0, \Exception $previous = null)
    {
        if (is_array($allowedType)) {
            $lastType = array_pop($allowedType);
            $allowedType = implode(', ', $allowedType);
            $allowedType .= 'or '.$lastType;
        }

        parent::__construct(
            sprintf(
                "The filter must be %s",
                $allowedType
            ),
            $code,
            $previous
        );
    }
}
