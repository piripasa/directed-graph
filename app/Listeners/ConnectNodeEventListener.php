<?php

namespace App\Listeners;

use App\Events\ConnectNodeEvent;
use App\Extensions\RedisHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConnectNodeEventListener
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
     * @param  ConnectNodeEvent  $event
     * @return void
     */
    public function handle(ConnectNodeEvent $event)
    {
        $fromNode = $event->fromNode;
        $toNode = $event->toNode;

        $this->client->publish('graph-channel', json_encode(
                [
                    'event'=>'nodeConnected',
                    'fromNode' => [
                        'id' => $fromNode['id'],
                        'name' => $fromNode['name'],
                    ],
                    'toNode' => [
                        'id' => $toNode['id'],
                        'name' => $toNode['name'],
                    ]
                ])
        );
    }
}
