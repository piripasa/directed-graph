<?php

namespace Tests;

use App\Models\Edge;
use App\Models\Node;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Predis\Client;

class GraphTest extends TestCase
{
    use DatabaseTransactions;

    private $urlPlaceholders = [
        'create' => [
            'method' => 'post',
            'url' => '/api/nodes',
        ],
        'update' => [
            'method' => 'put',
            'url' => '/api/nodes/%s',
        ],
        'delete' => [
            'method' => 'delete',
            'url' => '/api/nodes/%s'
        ],
        'connect' => [
            'method' => 'post',
            'url' => '/api/nodes/connect'
        ],
        'path' => [
            'method' => 'get',
            'url' => '/api/nodes/paths'
        ]
    ];

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Check if Redis server running or not
     */
    public function testRedisConnection()
    {
        $redis = new Client([
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST'),
            'port' => 6379
        ]);

        $this->assertNull($redis->connect());
    }

    public function test_name_is_required()
    {
        $response = $this->createNodeRequest([
            'name' => '',
        ]);

        $this->assertEquals(422, $response->status());
    }

    public function test_name_can_not_be_more_than_100()
    {
        $response = $this->createNodeRequest([
            'name' => str_random(101),
        ]);

        $this->assertEquals(422, $response->status());
    }

    public function test_node_can_be_created()
    {
        $response = $this->createNodeRequest([
            'name' => str_random(30),
        ]);

        $this->assertEquals(201, $response->status());
    }

    public function test_node_can_be_updated()
    {
        $response = $this->updateNodeRequest([
            'name' => str_random(30),
        ]);

        $this->assertEquals(200, $response->status());
    }

    public function test_node_can_be_deleted()
    {
        $response = $this->deleteNodeRequest();

        $this->assertEquals(200, $response->status());
    }

    public function test_node_can_not_be_same()
    {
        $node = factory(Node::class)->create([
            'name' => str_random(30),
        ]);

        $response = $this->connectNodeRequest([
            'from_node' => $node->id,
            'to_node' => $node->id,
        ]);

        $this->assertEquals(400, $response->status());
    }

    public function test_node_already_connected()
    {
        $node1 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);
        $node2 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);
        factory(Edge::class)->create([
            'from_node_id' => $node1->id,
            'to_node_id' => $node2->id,
        ]);

        $response = $this->connectNodeRequest([
            'from_node' => $node1->id,
            'to_node' => $node2->id,
        ]);

        $this->assertEquals(400, $response->status());
    }

    public function test_node_can_be_connected()
    {
        $node1 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);
        $node2 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);

        $response = $this->connectNodeRequest([
            'from_node' => $node1->id,
            'to_node' => $node2->id,
        ]);

        $this->assertEquals(200, $response->status());
    }

    public function test_node_shortest_path_not_found()
    {
        $node1 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);
        $node2 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);

        $response = $this->pathNodeRequest([
            'from_node' => $node1->id,
            'to_node' => $node2->id,
        ]);

        $this->assertEquals(400, $response->status());
    }

    public function test_node_shortest_path_found()
    {
        $node1 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);
        $node2 = factory(Node::class)->create([
            'name' => str_random(30),
        ]);

        factory(Edge::class)->create([
            'from_node_id' => $node1->id,
            'to_node_id' => $node2->id,
        ]);

        $response = $this->pathNodeRequest([
            'from_node' => $node1->id,
            'to_node' => $node2->id,
        ]);

        $this->assertEquals(200, $response->status());
    }

    /**
     * Send create node request.
     *
     * @param $formData
     *
     * @return $this
     */
    private function createNodeRequest($formData)
    {
        $data = $this->urlPlaceholders['create'];
        $method = $data['method'];
        $url = $data['url'];

        return $this->$method($url, $formData);
    }

    /**
     * Send update node request.
     *
     * @param $formData
     *
     * @return $this
     */
    private function updateNodeRequest($formData)
    {
        $data = $this->urlPlaceholders['update'];
        $method = $data['method'];
        $node = factory(Node::class)->create([
            'name' => str_random(30),
        ]);

        $url = sprintf($data['url'], $node->id);

        return $this->$method($url, $formData);
    }

    /**
     * Send delete node request.
     *
     * @param $formData
     *
     * @return $this
     */
    private function deleteNodeRequest()
    {
        $data = $this->urlPlaceholders['delete'];
        $method = $data['method'];
        $node = factory(Node::class)->create([
            'name' => str_random(30),
        ]);

        $url = sprintf($data['url'], $node->id);

        return $this->$method($url);
    }

    /**
     * Send connect node request.
     *
     * @param $formData
     *
     * @return $this
     */
    private function connectNodeRequest($formData)
    {
        $data = $this->urlPlaceholders['connect'];
        $method = $data['method'];

        $url = $data['url'];

        return $this->$method($url, $formData);
    }

    /**
     * Send get node shortest path request.
     *
     * @param $formData
     *
     * @return $this
     */
    private function pathNodeRequest($formData)
    {
        $data = $this->urlPlaceholders['path'];
        $method = $data['method'];

        $url = $data['url'] . '?' . http_build_query($formData);

        return $this->$method($url);
    }
}
