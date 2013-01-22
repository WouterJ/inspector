<?php

namespace Inspector\Exception\Filter;

/**
 * Exception thrown when there are problems with the filter,
 * but it shouldn't stop the application.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class ContinueException extends \RunTimeException
{ }
