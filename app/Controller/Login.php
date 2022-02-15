<?php
namespace App\Controller;

use App\Model\UserModel;
use App\Utils\View;
use App\Session\Login as SessionLogin;

class Login{

    /**
     * Renderiza apagina de Login
     */
    public static function getLogin()
    {
        return View::render('/pages/login/login',[
            "erro" => ""
        ]);
    }

    /**
     * Efectua o login
     */
    public static function setLogin($request)
    {
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $password = $postVars['password'] ?? '';

        if(empty($email) || empty($password)){
            return View::render('/pages/login/login',[
                "erro" => "Preencha todos os campos!"
            ]);    
        }

        $userModel = new UserModel();

        $user = $userModel->getUserByEmail($email);
        if(!isset($user['id'])){
            return View::render('/pages/login/login',[
                "erro" => "Email ou Senha Inválidos!"
            ]);
        }
        
        if(!password_verify($password,$user['password'])){
            return View::render('/pages/login/login',[
                "erro" => "Senha Inválida!"
            ]);
        }

        if($user['status'] == '0'){
            return View::render('/pages/login/login',[
                "erro" => "Falha ao Fazer Login!"
            ]);
        }


        SessionLogin::login($user);
        $request->getRouter()->redirect('/');
    }


    public static function setLogout($request)
    {
        if(!SessionLogin::isLogged())
        {
            return;
        }
        unset($_SESSION['usuario']);
        $request->getRouter()->redirect('/login');
    }

}