<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleGzipInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->header('Content-Encoding') === 'gzip') {
            $input = gzdecode(file_get_contents('php://input'));
            if ($input !== false) {
                $request->merge(json_decode($input, true));
            }
        }

        return $next($request);

    }
}
