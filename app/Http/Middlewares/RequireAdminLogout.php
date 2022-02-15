<?php
namespace App\Http\Middlewares;

use Exception;
use App\Session\Login as SessionLogin;
class RequireAdminLogout{


    public function handle($request,$next)
    {
        
        if(SessionLogin::isLogged())
        {
            $request->getRouter()->redirect('/');
        }
        
        return $next($request);
    }
}