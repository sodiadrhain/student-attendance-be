<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class UserAuth
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
        $header = $request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            $token = Str::substr($header, 7);
            if ($token === $this->token()) {
                return $next($request);
            }
            return response()->json(["error" => "You do not have access to this resource"], 401);
        }
        return response()->json(["error" => "You do not have access to this resource"], 401);
    }

    public function token()
    {
        return env('API_ACCESS_TOKEN');
    }
}
