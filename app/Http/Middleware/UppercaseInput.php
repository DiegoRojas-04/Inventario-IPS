<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UppercaseInput
{
    public function handle($request, Closure $next)
    {
        $request->merge(array_map(function ($value) {
            return is_string($value) ? strtoupper($value) : $value;
        }, $request->all()));

        return $next($request);
    }
}