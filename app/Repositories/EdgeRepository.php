<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 11:35 AM
 */

namespace App\Repositories;


use App\Models\Edge;

class EdgeRepository
{
    private $model;

    public function __construct(Edge $edge)
    {
        $this->model = $edge;
    }

    public function createNewEdge(array $data)
    {
        $edge = $this->model;

        foreach ($data as $field => $value) {
            $edge->{$field} = $value;
        }

        $edge->save();

        return $edge;
    }

    public function checkIfEdgeExists($fromNodeId, $toNodeId)
    {
        return $this->model
            ->where(function ($query) use ($fromNodeId, $toNodeId) {
                $query->where('from_node_id', $fromNodeId)
                    ->where('to_node_id', $toNodeId);
            })->orWhere(function ($query) use ($fromNodeId, $toNodeId) {
                $query->where('from_node_id', $toNodeId)
                    ->where('to_node_id', $fromNodeId);
            })
            ->first();
    }


    public function allEdges()
    {
        return $this->model->get();
    }
}