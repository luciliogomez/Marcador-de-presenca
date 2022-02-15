<?php
namespace App\Controller;
use App\Utils\View;
use App\Model\UserModel;
use WilliamCosta\DatabaseManager\Pagination;
use App\Utils\Alert;

class Usuario extends Template{


    /**
     * Metodo responsavel por renderizar a pagina com a lista de usuarios
     * @param object $request  a requisicao
     * 
     */
    public static function index($request)
    {   
        $userModel = new UserModel();
        $usuarios = [];
        $pagination = null;
        $queryParams = $request->getQueryParams();
        try{

            $total = count($userModel->getAll());

            $page = (isset($queryParams['page'])) ? $queryParams['page'] : 1;
            $pagination = new Pagination($total,$page,4);

            $usuarios = $userModel->getAll($pagination->getLimit());
        }catch(\Exception $ex)
        {
            $usuarios = [];
            // $pagination = '';
        }

        $tabela = self::getTabela(['ID','NOME','EMAIL','ACESSO','ACÇÕES'],$usuarios,
                                    "/usuario","id",0,false,false,false,false,false,true);

        $pagination = ($pagination!=null)?self::getPagination($pagination,$request):'';

        $content = View::render('pages/usuarios/lista',[
            'tabela' => $tabela,
            'pagination' => $pagination,
            'status'     => self::getStatus($request)
        ]);

        return self::getTemplate("Utilizadores","Utilizadores","Lista de Utilizadores",$content);
    }

    /**
     * Metodo reponsavel por enderizar a pagina de cadastro de usuario
     */
    public static function getNewUser($request)
    {
        $content = View::render("pages/usuarios/novo",[
            'acessos' => self::getAcessos(),
            'erro'   => self::getStatus($request),
            'nome'   => '',
            'email'  => ''
        ]);

        return self::getTemplate("Utilizadores","Utilizador","ADICIONAR UTILIZADOR",$content);
    }

    /**
     * Metodo responsavel poe preencher um caixa de selecao com os tipos de niveis de acesso
     */
    public static function getAcessos(){
        return self::getSelectInput([
            ["id" => "docente","descricao"=>"Docente"],
            ["id" => "admin", "descricao" => "Admin"]
        ],'acesso','Escolha o Acesso','id','descricao');
    }


    /**
     * Metodo responsavel por cadastrar um novo usuario
     * @param object $request a requisicao
     */
    public static function setNewUser($request)
    {
        $postVars = $request->getPostVars();
        if(empty($postVars['nome']) || empty($postVars['email'])){
            $request->getRouter()->redirect("/novo-usuario?status=empty");
        }

        $nome  = $postVars['nome'];
        $email = $postVars['email'];
        $acesso = $postVars['acesso'];

        $userModel = new UserModel();
        try{
            if($userModel->insert($nome,$email,$acesso)){
                $request->getRouter()->redirect("/novo-usuario?status=created");    
            }else{
                $request->getRouter()->redirect("/novo-usuario?status=failed");
            }
        }catch(\Exception $ex)
        {
            $request->getRouter()->redirect("/novo-usuario?status=failed");
        }

        $content = View::render("pages/usuarios/novo",[
            'acessos' => self::getAcessos(),
            'erro'   => '',
            'nome'   => '',
            'email'  => ''
        ]);

        return self::getTemplate("Utilizadores","Utilizador","ADICIONAR UTILIZADOR",$content);
    }


    /**
     * Metodo responsavel por bloquear um usuario
     * @param object $request a requisicao
     * @param int $idUsuario o ID do Usuario
     */
    public static function bloquearUser($request,$idUsuario)
    {
        $userModel = new UserModel();
        try{
            if($userModel->bloquerUtilizador($idUsuario))
            {
                $request->getRouter()->redirect("/usuarios?status=blocked");
            }else{
                $request->getRouter()->redirect("/usuarios?status=failed");
            }
        }catch(\Exception $ex)
        {
            $request->getRouter()->redirect("/usuarios?status=failed");
        }
    }


    /**
     * Metodo responsavel por desbloquear um usuario
     * @param object $request a requisicao
     * @param int $idUsuario o ID do Usuario
     */
    public static function desbloquearUser($request,$idUsuario)
    {
        $userModel = new UserModel();
        try{
            if($userModel->desbloquerUtilizador($idUsuario))
            {
                $request->getRouter()->redirect("/usuarios?status=unblocked");
            }else{
                $request->getRouter()->redirect("/usuarios?status=failed");
            }
        }catch(\Exception $ex)
        {
            $request->getRouter()->redirect("/usuarios?status=failed");
        }
    }


    /**
     * Metodo responsável por renderizar o perfil do usuário logado
     * @param object $request a requisicao
     */
    public static function getUserPerfil($request)
    {
        $content = View::render("pages/usuarios/perfil",[
            'nome' => $_SESSION['usuario']['name'],
            'email'   => $_SESSION['usuario']['email'],
            'acesso'   => $_SESSION['usuario']['acess'],
            'status'   => self::getStatus($request)
            // 'email'  => ''
        ]);

        return self::getTemplate("Utilizadores","Utilizador","MEU PERFIL",$content);
    }

    /**
     * Metodo responsavel por alterar a senha do usuario logado
     * @param object $request a requisicao
     */
    public static function getAlterarSenha($request)
    {   
        $userModel = new UserModel();

        $postVars = $request->getPostVars();
        if(empty($postVars['senha']))
        {
            $request->getRouter()->redirect("/perfil?status=empty");
        }

        try{
            if($userModel->alterarSenha($_SESSION['usuario']['id'],$postVars['senha']))
            {
                $request->getRouter()->redirect("/perfil?status=alterada");
            }else{
                $request->getRouter()->redirect("/perfil?status=failed");
            }
        }catch(\Exception $ex)
        {
            $request->getRouter()->redirect("/perfil?status=failed");
        }

    }


    private static function getStatus($request)
    {
        $queryParams = $request->getQueryParams();
        if(!isset($queryParams['status'])) return '';

        switch($queryParams['status'])
        {
            case 'created':
                return Alert::getSucess("Utilizador Adicionado com sucesso");
                break;
            case 'deleted':
                return Alert::getSucess("Utilizador Eliminado com sucesso");
                break;
            case 'duplicated':
                return Alert::getError("Já existe um Utilizador com esse email");
                break;
            case 'failed':
                return Alert::getError("Ocorreu Um Erro!");
                break;
            case 'empty':
                  return Alert::getError("Preencha todos os campos!");
                  break;
            case 'blocked':
              return Alert::getSucess("Utilizador Bloqueado com sucesso");
              break;
            case 'unblocked':
              return Alert::getSucess("Utilizador Desloqueado com sucesso");
              break;
            case 'alterada':
              return Alert::getSucess("Senha Alterada com sucesso");
              break;
        }
        
    }

}