<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConnectNodeRequest;
use App\Http\Requests\CreateNodeRequest;
use App\Http\Requests\ShortestPathRequest;
use App\Http\Requests\UpdateNodeRequest;
//use App\Services\EdgeService;
use App\Services\NodeService;
use App\Transformers\EdgeTransformer;
use App\Transformers\NodeTransformer;
use App\Transformers\PathTransformer;
use Illuminate\Http\Request;

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
            return response()->success(
                $this->edgeTransformer->transformCollection($response)
            );
        }
        return response()->success(
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
        return response()->created([
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
        return response()->success(
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
        return response()->success([
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
        return response()->success([
            'message' => 'Node deleted',
            'data' => $this->nodeTransformer->transform($this->nodeService->deleteNode($id))
        ]);
    }

    public function connect(ConnectNodeRequest $request)
    {

        if ($this->nodeService->connectNode($request->post('from_node'), $request->post('to_node')))
            return response()->success([
                'message' => 'Node connected'
            ]);

        return response()->error([
            'message' => 'Something went wrong'
        ]);
    }

    public function shortestPath(ShortestPathRequest $request)
    {
        $response = $this->nodeService->getShortestPath($request->get('from_node'), $request->get('to_node'));

        if (!empty($response)) {
            return response()->success([
                'message' => 'Path found',
                'data' => app(PathTransformer::class)->transform($response)
            ]);
        }

        return response()->error([
            'message' => 'Path not fond'
        ]);
    }

}
