<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 11:35 AM
 */

namespace App\Repositories;


use App\Models\Node;

class NodeRepository
{
    private $model;

    public function __construct(Node $node)
    {
        $this->model = $node;
    }

    public function indexOrSearchNode(array $queries, $perPage = 20)
    {
        return $this->model->paginate($perPage);
    }

    public function saveNewNode(array $data)
    {
        $node = $this->model;

        foreach ($data as $field => $value) {
            $node->{$field} = $value;
        }

        $node->save();

        return $node;
    }

    public function checkIfNodeExists($nodeId)
    {
        return $this->model->find($nodeId);
    }

    public function connect($fromNode, $toNode)
    {
        return $fromNode->neighbours()->insert([
            'from_node_id' => $fromNode->id,
            'to_node_id' => $toNode->id,
        ]);
    }

    public function updateNode($node, $data)
    {
        foreach ($data as $field => $value) {
            $node->{$field} = $value;
        }

        $node->save();

        return $node;
    }

    public function deleteNode($node)
    {
        if ($node->connectedTo)
            $node->connectedTo()->delete();
        if ($node->connectedFrom)
            $node->connectedFrom()->delete();

        return $node->delete();
    }

    public function fetchNeighbours($node, $perPage)
    {
        return $node->neighbours()->paginate($perPage);
    }

    public function getAllNodes()
    {
        return $this->model->with('neighbours')->get();
    }

}