<?php
namespace App\Http\Middlewares;

use Exception;
use App\Session\Login as SessionLogin;
class RequireAdminLogin{


    /**
     * Midleware para restringir o acesso apenas a utilizadores logados no sistema
     */
    public function handle($request,$next)
    {   
        if(!SessionLogin::isLogged())
        {
            $request->getRouter()->redirect('/login');
        }
        
        return $next($request);
    }
}