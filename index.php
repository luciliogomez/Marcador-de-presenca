<?php

require __DIR__."/vendor/autoload.php";
use App\Http\Router;
use App\Controller\Page;
use App\Controller\Home;
use App\Controller\Estudante;
use App\Controller\Turma;
use App\Controller\Marcacao;
use App\Controller\Usuario;
use App\Http\Middlewares\Maintenance;
use App\Http\Request;
use App\Http\Response;
use App\Http\Middlewares\RequireAdminLogout;
use App\Http\Middlewares\RequireAdminLogin;
use App\Http\Middlewares\RequireAdminAccess;
use App\Utils\View;
use WilliamCosta\DotEnv\Environment;
use App\Http\Middlewares\Queue as MiddlewareQueue;

Environment::load(__DIR__);

define("HOJE",date("Y-m-d"));
define("URL",getenv("URL"));

View::init([
    'URL'=> URL
]
);

MiddlewareQueue::setMap([
    'maintenance' => Maintenance::class,
    'require-admin-logout' => RequireAdminLogout::class,
    'require-admin-login'  => RequireAdminLogin::class,
    "require-admin-access" => RequireAdminAccess::class
]);

MiddlewareQueue::setDeault([
    'maintenance' 
]);

$router = new Router(URL);




//ROTAS 

// Rota Home - a pagina inicial
$router->get("/",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function(){
        return new Response(200,Home::getHome());
    }
]);


// Rota para a pagina que renderiza a lista de estudantes
$router->get("/estudantes",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Estudante::index($request));
    }
]);


// Rota renderiza a pagina de cadastro de estudantes
$router->get("/novoestudante",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function(){
        return new Response(200,Estudante::novo());
    }
]);


// Rota que faz o cadastro de estudante 
$router->post("/novoestudante",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Estudante::insert($request));
    }
]);

// Rota para a pagina de visualizacao de estudante [não chegou a ser usada]
$router->get("/estudante/{idEstudante}/view",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idEstudante){
        return new Response(200,Estudante::index($request));
    }
]);

// Rota para a pagina remoção de um estudante de certa turma[GET]
$router->get("/estudante/{idEstudante}/delete",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idEstudante){
        return new Response(200,Estudante::getDeleteStudent($request,$idEstudante));
    }
]);

// Rota que remove um estudante de certa turma [POST]
$router->post("/estudante/{idEstudante}/delete",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idEstudante){
        return new Response(200,Estudante::setDeleteStudent($request,$idEstudante));
    }
]);



// //   ROTAS DE TURMAS   ////

// rota que renderiza a lista de turmas
$router->get("/turmas",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Turma::index($request));
    }
]);



// rota que renderiza o formulario de cadastro de novas turmas
$router->get("/nova-turma",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Turma::getNewTurma($request));
    }
]);


// rota que cadastra nova turma
$router->post("/nova-turma",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Turma::setNewTurma($request));
    }
]);

// rota que renderiza a pagina  para confirmacao de eliminacao de uma turma
$router->get("/turma/{idTurma}/delete",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idTurma){
        return new Response(200,Turma::getDeleteTurma($request,$idTurma));
    }
]);


// rota que remove uma turma
$router->post("/turma/{idTurma}/delete",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idTurma){
        return new Response(200,Turma::setDeleteTurma($request,$idTurma));
    }
]);


// rota que renderiza a lista de estudantes de uma turma
$router->get("/turma/{idTurma}/view",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idTurma){
        return new Response(200,Turma::getViewTurma($request,$idTurma));
    }
]);

// rota para adicionar estudantes em uma certa turma
//rota que renderiza a lista de estudantes(todos), que podem ser adicionados em uma turma
$router->get("/adicionar-estudante-turma/{idTurma}",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idTurma){
        return new Response(200,Turma::getAddEstudantesNaTurma($request,$idTurma));
    }
]);


// rota para adicionar estudantes em uma certa turma
$router->get("/add-estudante/{idEstudante}/turma/{idTurma}",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idEstudante,$idTurma){
        return new Response(200,Turma::getIncluirEstudanteNaTurma($request,$idEstudante,$idTurma));
    }
]);



// ROTA PARA MARCACOES

// rota que renderiza a pagina de marcacoes
$router->get("/marcacoes",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Marcacao::index($request));
    }
]);


// rota que busca marcacoes de uma turma feitas numa determinada data
$router->post("/marcacoes",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Marcacao::getMarcacoes($request));
    }
]);

// rota que renderiza a pagina de cadastro de novas marcacoes
$router->get("/nova-marcacao",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Marcacao::getNewMarcacao($request));
    }
]);


// rota que busca estudantes sem marcacao em certa turma e no dia corrente
$router->post("/nova-marcacao",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Marcacao::getEstudantesParaMarcacao($request));
    }
]);


// rota para marcar o estado "PRESENTE" de um aluno
$router->get("/marcacao/turma/{idTurma}/estudante/{idEstudante}/presente",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idTurma,$idEstudante,$estado){
        return new Response(200,Marcacao::marcar($request,$idTurma,$idEstudante,1));
    }
]);

// rota para marcar o estado "AUSENTE" de um aluno
$router->get("/marcacao/turma/{idTurma}/estudante/{idEstudante}/ausente",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request,$idTurma,$idEstudante,$estado){
        return new Response(200,Marcacao::marcar($request,$idTurma,$idEstudante,0));
    }
]);



// ROTA PARA GERACAO DE RELATORIOS

// rota para renderizar a pagina de geracao de relatorios
$router->get("/relatorio",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Marcacao::getRelatorio($request));
    }
]);


// rota para gerar o relatorio de marcacoes
$router->post("/relatorio",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Marcacao::gerarRelatorio($request));
    }
]);


// ROTAS DE USUARIOS [somente disponivel para utilizadore que tenham o nivel de acesso "admin"]

// rota para renderizar a pagina de usuarios 
$router->get("/usuarios",[
    'middlewares'=>[
        'require-admin-login',
        "require-admin-access"
    ],
    function($request){
        return new Response(200,Usuario::index($request));
    }
]);


// rota para renderizar o formulario de cadastro de usuarios
$router->get("/novo-usuario",[
    'middlewares'=>[
        'require-admin-login',
        "require-admin-access"
    ],
    function($request){
        return new Response(200,Usuario::getNewUser($request));
    }
]);


// rota para bloquear usuario [Não poderá fazer login no sistema]
$router->get("/usuario/{idUsuario}/bloquear",[
    'middlewares'=>[
        'require-admin-login',
        "require-admin-access"
    ],
    function($request,$idUsuario){
        return new Response(200,Usuario::bloquearUser($request,$idUsuario));
    }
]);

// rota para desbloquear utilizadores [poderá fazer login no sistema]
$router->get("/usuario/{idUsuario}/desbloquear",[
    'middlewares'=>[
        'require-admin-login',
        "require-admin-access"
    ],
    function($request,$idUsuario){
        return new Response(200,Usuario::desbloquearUser($request,$idUsuario));
    }
]);


// rota para renderizar o formulario de cadastro de utilizadores
$router->post("/novo-usuario",[
    'middlewares'=>[
        'require-admin-login',
        "require-admin-access"
    ],
    function($request){
        return new Response(200,Usuario::setNewUser($request));
    }
]);


// rota para renderizar o perfil do utilizador logado no sistema
$router->get("/perfil",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Usuario::getUserPerfil($request));
    }
]);


// rota para alterar senha do utilizador logado no sistema
$router->post("/perfil",[
    'middlewares'=>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Usuario::getAlterarSenha($request));
    }
]);


// INCLUI AS ROTAS DE LOGIN
include_once __DIR__."/routes/admin.php";


$router->run()->sendResponse();

?>
