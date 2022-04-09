<?php

namespace App\Http\Middleware;

use app\Helpers\jwtAuth;
use Closure;
use Illuminate\Http\Request;

class ApiAuthmiddelware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $res, Closure $next)
    {
        //COMPROBAR SI EL USUARIO ESTA IDENTIFICADO
        $token = $res->header('Authorization');
        $jwtauth = new jwtAuth();
        $checkToken = $jwtauth->checktoken($token);

        if ($checkToken && !empty($checkToken)) {

            return $next($res);
        } else {
            $Data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no se a identificado'
            );
            return response()->json($Data, $Data['code']);
        }
    }
}
