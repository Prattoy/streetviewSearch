<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

  public function handle($request, Closure $next)
  {
    // return $next($request)
    //   ->header('Access-Control-Allow-Origin', '*')
    //   ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    //   ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');

      $response = $next($request);

      $response->headers->set('Access-Control-Allow-Origin' , '*');
      $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
      $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application, Origin');
      $response->headers->set('Access-Control-Allow-Credentials', true);
      
      // res.header('Access-Control-Allow-Origin', '*');
      // res.header('Access-Control-Allow-Methods', 'DELETE, GET, POST, PUT, OPTIONS');
      // res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
      // res.header('Access-Control-Allow-Credentials', true);

      return $response;
  }

}
