<?php

namespace App\Listeners;

use App\Events\DeleteNodeEvent;
use App\Extensions\RedisHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteNodeEventListener
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
     * @param  DeleteNodeEvent  $event
     * @return void
     */
    public function handle(DeleteNodeEvent $event)
    {
        $node = $event->node;

        $this->client->publish('graph-channel', json_encode(
                [
                    'event'=>'nodeDeleted',
                    'node' => [
                        'id' => $node['id'],
                        'name' => $node['name'],
                    ]
                ])
        );
    }
}
