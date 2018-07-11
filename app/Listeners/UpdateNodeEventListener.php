<?php

namespace App\Listeners;

use App\Events\UpdateNodeEvent;
use App\Extensions\RedisHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateNodeEventListener
{
    protected $client;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(RedisHelper $redisHelper)
    {
        $this->client = $redisHelper;
    }

    /**
     * Handle the event.
     *
     * @param  UpdateNodeEvent  $event
     * @return void
     */
    public function handle(UpdateNodeEvent $event)
    {
        $node = $event->node;

        $this->client->publish('graph-channel', json_encode(
                [
                    'event'=>'nodeUpdated',
                    'node' => [
                        'id' => $node['id'],
                        'name' => $node['name'],
                    ]
                ])
        );
    }
}
