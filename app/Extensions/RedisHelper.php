<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 5:13 PM
 */

namespace App\Extensions;

use Predis\Client;

class RedisHelper
{
    protected $client;

    /**
     * RedisHelper constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => 6379
        ]);
    }


    public function publish($channel, $data)
    {
        $this->client->publish($channel, $data);
    }
}