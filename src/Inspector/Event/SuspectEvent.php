<?php

namespace Inspector\Event;

use Inspector\Iterator\Suspects;

use Symfony\Component\EventDispatcher\Event;

class SuspectEvent extends Event
{
    /**
     * @var Suspects
     */
    private $suspects;

    /**
     * @param Suspects $suspects
     */
    public function setSuspects(Suspects $suspects)
    {
        $this->suspects = $suspects;
    }

    /**
     * @return Suspects
     */
    public function getSuspects()
    {
        return $this->suspects;
    }
}
