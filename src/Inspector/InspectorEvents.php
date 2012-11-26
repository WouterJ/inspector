<?php

namespace Inspector;

final class InspectorEvents
{
    /**
     * The FIND event occurs when the inspector inspected the directory
     * and listed the files.
     *
     * This event allows you to tweak the list of files before marking
     * them as suspect. The event listener method receives a
     * Inspector\Event\FileListEvent instance
     *
     * @var string
     */
    const FIND = 'inspector.find';

    /**
     * The MARK event occurs when the inspector had marked the list of files
     * as a suspect.
     *
     * This event allows you to tweak the suspects iterator before returning
     * it as suspects. The event listener method receives a
     * Inspector\Event\SuspectEvent instance
     */
    const MARK = 'inspector.mark';
}
