<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 11:34 AM
 */

namespace App\Services;


use App\Events\ConnectNodeEvent;
use App\Events\CreateNodeEvent;
use App\Events\DeleteNodeEvent;
use App\Events\UpdateNodeEvent;
use App\Exceptions\InvalidArgumentException;
use App\Exceptions\NotFoundException;
use App\Repositories\EdgeRepository;
use App\Repositories\NodeRepository;
use Illuminate\Http\Request;

class NodeService
{
    private $repository;
    private $edgeRepository;

    public function __construct(NodeRepository $repository, EdgeRepository $edgeRepository)
    {
        $this->repository = $repository;
        $this->edgeRepository = $edgeRepository;
    }

    public function getNodeList(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $perPage = 1 > $perPage || $perPage > 100 ? 20 : $perPage;
        $nodeId = $request->get('node_id', false);
        if ($nodeId) {
            $node = $this->repository->checkIfNodeExists($nodeId);
            if (!$node)
                throw new NotFoundException('Node not found');
            return $this->repository->fetchNeighbours($node, $perPage);
        }
        return $this->repository->indexOrSearchNode([], $perPage);
    }

    public function getSingleNode($id)
    {
        return $this->repository->checkIfNodeExists($id);
    }

    public function sanitizeAndInsertNodeInformation(Request $request)
    {
        $nodeInputToModelMapper = [];
        $nodeInputToModelMapper['name'] = trim($request->post('name'));

        $node = $this->repository->saveNewNode($nodeInputToModelMapper);

        event(new CreateNodeEvent($node->toArray()));

        return $node;
    }

    public function connectNode($fromNodeId, $toNodeId)
    {
        if ($fromNodeId == $toNodeId)
            throw new InvalidArgumentException('Both node can\'t be same');

        $fromNode = $this->repository->checkIfNodeExists($fromNodeId);
        $toNode = $this->repository->checkIfNodeExists($toNodeId);

        if (!$fromNode || !$toNode)
            throw new NotFoundException('Node not found');


        if ($this->edgeRepository->checkIfEdgeExists($fromNodeId, $toNodeId))
            throw new InvalidArgumentException('Already connected');


        $return = $this->repository->connect($fromNode, $toNode);
        event(new ConnectNodeEvent($fromNode->toArray(), $toNode->toArray()));
        return $return;
    }

    public function sanitizeAndUpdateNodeInformation(Request $request, $nodeId)
    {
        $nodeInputToModelMapper = [];
        $nodeInputToModelMapper['name'] = trim($request->post('name'));

        $node = $this->repository->checkIfNodeExists($nodeId);

        if (!$node)
            throw new NotFoundException('Node not found');

        $this->repository->updateNode($node, $nodeInputToModelMapper);

        event(new UpdateNodeEvent($node->toArray()));

        return $node;
    }

    public function deleteNode($nodeId)
    {
        $node = $this->repository->checkIfNodeExists($nodeId);

        $this->repository->deleteNode($node);

        event(new DeleteNodeEvent($node->toArray()));

        return $node;
    }

    public function getShortestPath($fromNodeId, $toNodeId)
    {
        if ($fromNodeId == $toNodeId)
            throw new InvalidArgumentException('Both node can\'t be same');

        $fromNode = $this->repository->checkIfNodeExists($fromNodeId);
        $toNode = $this->repository->checkIfNodeExists($toNodeId);

        if (!$fromNode || !$toNode)
            throw new NotFoundException('Node not found');

        $nodes = $this->repository->getAllNodes()->toArray();
        $graph = [];
        foreach ($nodes as $node) {
            $neighbours = [];
            if (!empty($node['neighbours'])) {
                foreach ($node['neighbours'] as $neighbour) {
                    $neighbours[$neighbour['to_node_id']] = 1;
                }
            }
            $graph[$node['id']] = $neighbours;
        }
        //dd($graph);

        $graphService = new GraphService($graph);
        $pathArray = $graphService->shortestPaths($fromNodeId, $toNodeId);

        $ret = !empty($pathArray) ? $pathArray[0] : [];
        foreach ($ret as $key => $value) {
            $ret[$key] = searchArray($nodes, 'id', (int) $value);
        }
        //dd($ret);
        return $ret;
    }

}