<?php
namespace App\Session;

class Login{

    // METODO QUE INICIA A SECCAO
    private static function init()
    {
        if(session_status() != PHP_SESSION_ACTIVE)
        {
            session_start();
        }
    }


    public static function login($user)
    {

        self::init();
        $_SESSION['usuario'] = $user;
        return true;
    }


    public static function isLogged()
    {
        self::init();

        if(isset($_SESSION['usuario']['id'])){
            return true;
        }
        return false;
    }

}