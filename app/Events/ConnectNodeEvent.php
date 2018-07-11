<?php

namespace App\Events;

class ConnectNodeEvent extends Event
{
    public $fromNode;
    public $toNode;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $fromNode, array $toNode)
    {
        $this->fromNode = $fromNode;
        $this->toNode = $toNode;
    }
}
