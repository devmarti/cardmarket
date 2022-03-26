<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Token
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(isset($request->token)){
            $token = $request->token;
            if($user = User::where('token',$token)->first()){
                $response["msg"] = "Token valido";
                $request->user = $user;
                Log::info($response);
                return $next($request);
            }else{
                $response["msg"] = "Token invalido";
                Log::error($response);
            }

        }else{
            $response["msg"] = "Token no existe.";
            Log::error($response);
        }

        return response()->json($response);
    }
}

