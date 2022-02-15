<?php
namespace App\Http\Middlewares;

use Exception;

class Maintenance{


    public function handle($request,$next)
    {
        if(getenv('MAINTENANCE') == 'true')
        {
            throw new Exception("Esta pagina está em manutencao. Por favor tente mais tarde!");
        }
        
        return $next($request);
    }
}