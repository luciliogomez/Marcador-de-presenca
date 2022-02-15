<?php

use App\Http\Response;
use App\Controller\Home;
use App\Controller\Login;

// rota para a pagina de login
$router->get("/login",[
    'middlewares'=>[
        'require-admin-logout'
    ],
    function(){
        return new Response(200,Login::getLogin());
    }
]);

// rota que efectua o login
$router->post("/login",[
    function($request){
        return new Response(200,Login::setLogin($request));
    }
]);

// rota que efectua logout
$router->get("/logout",[
    
    function($request){
        return new Response(200,Login::setLogout($request));
    }
]);
