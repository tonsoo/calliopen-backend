<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if(!$token) {
            return response()->json(['error' => 'No token'], Response::HTTP_UNAUTHORIZED);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json(['error' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        // $request->route()->setParameter('client', $client);
        $request->setUserResolver(fn() => $accessToken->tokenable);

        return $next($request);
    }
}
