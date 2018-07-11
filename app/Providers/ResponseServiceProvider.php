<?php namespace App\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    public function boot(ResponseFactory $response)
    {
        # success related macros

        $response->macro('success', function ($data, $statusCode = 200) {
            return response()->json(responsableConverter($data), $statusCode);
        });

        $response->macro('created', function ($data) {
            return response()->json(responsableConverter($data), 201);
        });

        $response->macro('accepted', function ($data) {
            return response()->json(responsableConverter($data), 202);
        });

        $response->macro('noContent', function () {
            return response()->json([], 204);
        });

        $response->macro('deleted', function ($data) {
            return response()->json(responsableConverter($data), 200);
        });

        # Error related macros

        $response->macro('error', function ($data, $statusCode = 400) {
            return response()->json($data, $statusCode);
        });

        $response->macro('bad', function ($data) {
            return response()->json($data, 400);
        });

        $response->macro('unauthorized', function ($data) {
            return response()->json($data, 401);
        });

        $response->macro('forbidden', function ($data) {
            return response()->json($data, 403);
        });

        $response->macro('notFound', function ($data) {
            return response()->json($data, 404);
        });

        $response->macro('notAllowed', function ($data) {
            return response()->json($data, 406);
        });

        $response->macro('notAcceptable', function ($data) {
            return response()->json($data, 406);
        });

        $response->macro('validationError', function ($data) {
            return response()->json($data, 422);
        });

        $response->macro('tooManyRequests', function ($data) {
            return response()->json($data, 429);
        });

        # Service related macros

        $response->macro('wrong', function ($data) {
            return response()->json($data, 500);
        });
    }
}
