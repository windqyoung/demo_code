<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AccessControll
{
    /**
     * 根据设定给请求添加跨域头
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        // 有设置, 添加头
        $origin = config('cross.origin');
        $headers = [];
        if ($origin) {
            $headers = [
                'Access-Control-Allow-Origin' => $origin == '*' ? $request->header('origin', $origin) : $origin,
                'Access-Control-Allow-Credentials' => config('cross.credentials') ?: 'true',
                'Access-Control-Allow-Methods' => config('cross.methods') ?: 'POST,GET',
                'Access-Control-Allow-Headers' => config('cross.headers') ?: 'Content-Type',
            ];

            // OPTIONS 添加头以后, 直接返回
            if ($request->isMethod('OPTIONS')) {
                return (new Response())->withHeaders($headers);
            }

        }

        return $next($request)->withHeaders($headers);
    }
}
