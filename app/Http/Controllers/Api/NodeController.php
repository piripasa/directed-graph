<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConnectNodeRequest;
use App\Http\Requests\CreateNodeRequest;
use App\Http\Requests\ShortestPathRequest;
use App\Http\Requests\UpdateNodeRequest;
use App\Services\NodeService;
use App\Transformers\EdgeTransformer;
use App\Transformers\NodeTransformer;
use App\Transformers\PathTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NodeController extends Controller
{
    protected $nodeService;
    protected $nodeTransformer;
    protected $edgeTransformer;

    public function __construct(NodeService $nodeService, NodeTransformer $nodeTransformer, EdgeTransformer $edgeTransformer)
    {
        $this->nodeService = $nodeService;
        $this->nodeTransformer = $nodeTransformer;
        $this->edgeTransformer = $edgeTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = $this->nodeService->getNodeList($request);

        if ($request->get('node_id', false)) {
            return Response::success(
                $this->edgeTransformer->transformCollection($response)
            );
        }
        return Response::success(
            $this->nodeTransformer->transformCollection($response)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateNodeRequest $request)
    {
        return Response::created([
            'message' => 'Node created',
            'data' => $this->nodeTransformer->transform($this->nodeService->sanitizeAndInsertNodeInformation($request))
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Response::success(
            $this->nodeTransformer->transform($this->nodeService->getSingleNode($id))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNodeRequest $request, $id)
    {
        return Response::success([
            'message' => 'Node updated',
            'data' => $this->nodeTransformer->transform($this->nodeService->sanitizeAndUpdateNodeInformation($request, $id))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Response::success([
            'message' => 'Node deleted',
            'data' => $this->nodeTransformer->transform($this->nodeService->deleteNode($id))
        ]);
    }

    public function connect(ConnectNodeRequest $request)
    {

        if ($this->nodeService->connectNode($request->post('from_node'), $request->post('to_node')))
            return Response::success([
                'message' => 'Node connected'
            ]);

        return Response::error([
            'message' => 'Something went wrong'
        ]);
    }

    public function shortestPath(ShortestPathRequest $request)
    {
        $response = $this->nodeService->getShortestPath($request->get('from_node'), $request->get('to_node'));

        if (!empty($response)) {
            return Response::success([
                'message' => 'Path found',
                'data' => app(PathTransformer::class)->transform($response)
            ]);
        }

        return Response::error([
            'message' => 'Path not fond'
        ]);
    }

}
