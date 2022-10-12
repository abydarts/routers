<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Logger
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $log = new Log();
        $log->direction = 'IN';
        $log->method = $request->method();
        $log->request = [
            'header' => $request->headers->all(),
            'data' => $request->all(),
            'url' => $request->url()
        ];
        $log->save();

        $response = $next($request);

        if($response) {
            $log->response = [
                'header' => $response->headers->all(),
                'data' => ($json = json_decode($response->content(), true)) ? $json : ['content' => $response->content()]
            ];
            $log->save();
        }

        return $response;
    }
}
