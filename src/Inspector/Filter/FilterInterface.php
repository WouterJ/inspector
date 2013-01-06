<?php

namespace Inspector\Filter;

interface FilterInterface
{
    public function filter(\SplFileInfo $file);
}
