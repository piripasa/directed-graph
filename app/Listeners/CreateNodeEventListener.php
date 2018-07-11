<?php

namespace App\Listeners;

use App\Events\CreateNodeEvent;
use App\Extensions\RedisHelper;
use App\Transformers\NodeTransformer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateNodeEventListener
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
     * @param  CreateNodeEvent  $event
     * @return void
     */
    public function handle(CreateNodeEvent $event)
    {
        $node = $event->node;

        $this->client->publish('graph-channel', json_encode(
            [
                'event'=>'nodeCreated',
                'node' => [
                    'id' => $node['id'],
                    'name' => $node['name'],
                ]
            ])
        );
    }
}
