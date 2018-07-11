<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CreateNodeEvent' => [
            'App\Listeners\CreateNodeEventListener',
        ],
        'App\Events\UpdateNodeEvent' => [
            'App\Listeners\UpdateNodeEventListener',
        ],
        'App\Events\DeleteNodeEvent' => [
            'App\Listeners\DeleteNodeEventListener',
        ],
        'App\Events\ConnectNodeEvent' => [
            'App\Listeners\ConnectNodeEventListener',
        ],
    ];
}
