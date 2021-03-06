<?php

namespace App\Events;

class UpdateNodeEvent extends Event
{
    public $node;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $node)
    {
        $this->node = $node;
    }
}
