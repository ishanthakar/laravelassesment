<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckAdminRole
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
        
        
        if (empty(\Auth::guard('api')->user()) || \Auth::guard('api')->user()->user_role != config('global.ADMIN_ROLE')) {
            return response()->json(['message' => 'You can not perform this operation as you are not admin user!', 'data' => ['general' => 'You can not perform this operation as you are not admin user!']], 401);
        }
        return $next($request);
    }
}
