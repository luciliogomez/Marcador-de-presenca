<?php
namespace App\Http\Middlewares;

use Exception;
use App\Session\Login as SessionLogin;
class RequireAdminAccess{


    /**
     * Midleware para permitor o acesso apenas a utilizadores com o nivel de acesso "admin"
     */
    public function handle($request,$next)
    {
        
        if($_SESSION['usuario']['acess'] != 'admin')
        {
            $request->getRouter()->redirect('/');
        }
        
        return $next($request);
    }
}