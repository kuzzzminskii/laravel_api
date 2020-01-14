<?php

namespace App\Http\Middleware;

use App\Accesses;
use Closure;

class ApiAuthentication
{
    protected $collection     = null;
    protected $authentication = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authentication = $request->route()->parameters();

        if(!isset($this->authentication['token'])) {

            return $request->error = 'Specified token not exist';
        }

        $this->collection = Accesses::where('token', '=', $this->authentication['token'])->get();

        if(!count($this->collection)) {

            return $request->error = 'Specified token is not valid';
        }

        return $next($request);
    }
}
