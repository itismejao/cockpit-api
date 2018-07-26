<?php

namespace App\Http\Middleware;

use App\Http\Services\Vtrine as VtrineService;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyApiToken
{

    /**
     * Valida se o token ainda é válido com base no tempo de vida definido nas configurações.
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        $lastRequest = Auth::user()->last_request;
        $lastIp = Auth::user()->last_ip;

        $vtrineService = new VtrineService();
        $msgAccess = $vtrineService->collaboratorCanAccess(Auth::user()->uid);

        if ($msgAccess) {
            return response()->json(
                ['code' => 2, 'error' => 'Access not allowed at this time'],
                401
            );
        }

        /**
         * If the application is validating the IP and
         * IP of the request is different from the ip registered in the authentication
         */
        if ( (env('API_VALIDATE_IP', false) == true) and ($lastIp != $request->ip()) ) {
            Log::info('IP different: ' . Auth::user()->email . ' | Old IP: ' . $lastIp . ' | New IP: ' . $request->ip());

            return response()->json(
                ['code' => 4, 'error' => 'Ip conflict'],
                401
            );
        }

        if ($lastRequest) {
            $lastRequest = Carbon::createFromFormat('Y-m-d H:i:s', $lastRequest);
            $now = Carbon::now();

            $diff = $lastRequest->diffInMinutes($now);

            $apiTokenLifetime = (int)config('auth.api_token_lifetime');

            if ($apiTokenLifetime > $diff) {
                Auth::user()->newLastRequest();

                return $next($request);
            }
        }

        return response()->json(
            ['code' => 5, 'error' => 'Token expired'],
            401
        );
    }
}
