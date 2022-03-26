<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class OfertasPermission
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

        if($request->user->rol =='Particular' || $request->user->rol =='Profesional'){
            $response['msg'] = "Permisos validados";
            return $next($request);

        }else{
            $response['msg'] = "Permisos invalidados";
        }
        return response()->json($response);
    }
}
