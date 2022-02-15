<?php
namespace App\Controller;
use App\Model\TurmaModel;
use App\Utils\View;
use App\Model\Estudante as Student;
use App\Utils\Conexao;
use \Exception;
use WilliamCosta\DatabaseManager\Pagination;
class Estudante extends Template{

   
    /**
     * Metodo responsavel por renderizar a Pagina com a listagem de todos estudantes
     * @param object $request
     * @return string retorna a pagina
     */
    public static function index($request){
        $turmaModel = new TurmaModel;
        $id_user = (int) $_SESSION['usuario']['id'];

        $total = count($turmaModel->getMyEstudantesNaTurma($id_user));
          
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page']?? '1';
        
        $pagination = new Pagination($total,$page,4);

        $turmas = $turmaModel->getMyEstudantesNaTurma($id_user,$pagination->getLimit());
        $tabela = self::getTabela(["ID","Nome","Email","Turma","Accoes"],
                                    ($turmas),
                                        '/estudante',"id",0,false,false,true
                                    );

        $content = View::render("pages/estudantes/lista",[
            'tabela' => $tabela,
            'pagination' => self::getPagination($pagination,$request)
        ]);

        return self::getTemplate("Estudantes","Estudante","Meus Estudantes",$content);
    }
    

    /**
     * Metodo responsavel por renderizar o formulario para cadastro de estudante
     */
    public static function novo()
    {
        $content = View::render("pages/estudantes/novo",[
            'turmas' => self::getTurmas($_SESSION['usuario']['id']),
            'erro'   => '',
            'nome'   => '',
            'email'  => ''
        ]);

        return self::getTemplate("Estudantes","Estudante","ADICIONAR ESTUDANTE",$content);
    }

    

    /**
     * Metodo responsavel por cadastrar um novo estudante na base de dados
     */
    public static function insert($request)
    {
        $postVars = $request->getPostVars();
        if(    empty($postVars['nome']) || empty($postVars['email']) 
            || empty($postVars['turma']) || empty($postVars['data']) 
        ){
            $content = View::render("pages/estudantes/novo",[
                'turmas' => self::getTurmas(),
                'erro'   => 'Preencha os campos obrigatorios!',
                'nome'   =>  $postVars['nome'] ?? '',
                'email'  =>  $postVars['email'] ?? ''
            ]);
    
            return self::getTemplate("Estudantes","Estudante","ADICIONAR ESTUDANTE",$content);
        }

        $nome = htmlspecialchars($postVars["nome"]);
        $email = htmlspecialchars($postVars["email"]);
        $turma = htmlspecialchars($postVars["turma"]);
        $data = htmlspecialchars($postVars["data"]);
        
        $turmaModel = new TurmaModel();
        $student =  $turmaModel->getEstudanteByEmail($email);
        if($student != null){
            $content = View::render("pages/estudantes/novo",[
                'turmas' => self::getTurmas(),
                'erro'   => 'Email já em uso por outro Utilizador!',
                'nome'   =>  $postVars['nome'] ?? '',
                'email'  =>  $postVars['email'] ?? ''
            ]); 
            return self::getTemplate("Estudantes","Estudante","ADICIONAR ESTUDANTE",$content);
        
        }

        $transaction = Conexao::getInstance()->beginTransaction();

        try{
            $sql = "INSERT INTO estudantes(nome,email,data_de_nascimento) VALUES (:nome,:email,:data)";
            $stmt = Conexao::getInstance()->prepare($sql);
            $stmt->bindParam(":nome",$nome);
            $stmt->bindParam(":email",$email);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            if($stmt->rowCount() >=1 ){
                $id_estudante = Conexao::getInstance()->lastInsertId();
                $sql = "INSERT INTO estudante_na_turma(id_estudante,id_turma) VALUES (:id_estudante,:id_turma)";
                $stmt = Conexao::getInstance()->prepare($sql);
                $stmt->bindParam(":id_estudante",$id_estudante);
                $stmt->bindParam(":id_turma",$turma);
                // $stmt->bindParam(":data",$data);
                $stmt->execute();
                if($stmt->rowCount() >=1 ){
                    Conexao::getInstance()->commit();
                    $content = View::render("layout/result",[
                        'message' => "Cliente Adicionado com sucesso!",
                        'color'   => 'green'
                    ]);
                    return self::getTemplate("Estudantes","Resutados","Resultados",$content);
                }else{  
                    $erros[] = "";
                    Conexao::getInstance()->rollback();
                    $content = View::render("pages/estudantes/novo",[
                        'turmas' => self::getTurmas(),
                        'erro'   => 'Falha. Tente mais tarde!'
                    ]); 
                    return self::getTemplate("Estudantes","Estudante","ADICIONAR ESTUDANTE",$content);
                }

            }else{  
                Conexao::getInstance()->rollback();
                    $content = View::render("pages/estudantes/novo",[
                        'turmas' => self::getTurmas(),
                        'erro'   => 'Falha. Tente mais tarde!'
                    ]); 
                    return self::getTemplate("Estudantes","Estudante","ADICIONAR ESTUDANTE",$content);
            }
        }
        catch(Exception $ex){
                Conexao::getInstance()->rollback();
                    $content = View::render("pages/estudantes/novo",[
                        'turmas' => self::getTurmas(),
                        'erro'   => 'Falha. Tente mais tarde!'.$ex->getMessage()
                    ]); 
                    return self::getTemplate("Estudantes","Estudante","ADICIONAR ESTUDANTE",$content);
            }


    
    }


    /**
     * Metodo responsavel por renderizar a pagina de confirmação para remoção de estudante de uma turma
     * @param object $request a requisição
     * @param int $id_estudante O ID do estudante
     */
    public static function getDeleteStudent($request,$id_estudante)
    {
        $turmaModel = new TurmaModel();

        $estudante = $turmaModel->getEstudanteNaTurma($id_estudante);
        if($estudante == null)
        {
            $request->getRouter()->redirect('/estudantes?status=error');
        }

        $content = View::render('pages/estudantes/delete',[
            'nome'         => $estudante['nome'] ?? '',
            'turma'        => $estudante['descricao'] ?? '',
            'id_turma'     => $estudante['id_turma'] ?? '',
            'id_estudante' => $estudante['id'],
            'erro'         => ''
        ]);

        return self::getTemplate("Estudantes","Estudante","ELIMINAR ESTUDANTE",$content);
    }


    /**
     * Metodo que processa a eliminação de estudante
     */
    public static function setDeleteStudent($request,$id_estudante)
    {
        $turmaModel = new TurmaModel();

        $estudante = $turmaModel->getEstudanteNaTurma($id_estudante);
        if($estudante == null)
        {
            $request->getRouter()->redirect('/estudantes?status=error');
        }

        try{
            $postVars = $request->getPostVars();
            $idTurma     = $postVars['id_turma'];
            $idEstudante = $postVars['id_estudante'];

            if($turmaModel->deleteFromTurma($id_estudante,$idTurma))
            {
                $request->getRouter()->redirect('/estudantes?status=deleted');
            }else{
                $content = View::render("layout/result",[
                    'message' => "Cliente Não eliminado!",
                    'color'   => 'red'
                ]);
                return self::getTemplate("Estudantes","Resutados","Resultados",$content);
            }

        }catch(Exception $ex){
            $content = View::render("layout/result",[
                'message' => "Cliente Não eliminado!",
                    'color'   => 'red'
            ]);
            return self::getTemplate("Estudantes","Resutados","Resultados",$content);
        }


    }

    

    // metodo que pega o link para a pagina anterior da paginacao
    public static function getPrevLink($pages, $request){

        $links = '';
        foreach($pages as $page)
        {
            $pageNumber = 0;
            if($page['current']){
              $pageNumber = intval($page['page']);  
              
              if( ($pageNumber) == 1 ){
                $link = $request->getURI()."?page=".$page['page'];
                
                  return View::render("layout/pagination/item_left",[
                    'link' => $link,
                    'disabled' => 'disabled'
                  ]);
              }else{
                $link = $request->getURI()."?page=".($pageNumber - 1);
               
                return View::render("layout/pagination/item_left",[
                  'link' => $link,
                  'disabled' => ''
                ]);
              }
            
            }
        }
    }

    // metodo que pega o link para a pagina seguinte da paginacao
    public static function getNextLink($pages, $request){

        $links = '';
        foreach($pages as $page)
        {
            $pageNumber = 0;
            if($page['current']){
              $pageNumber = intval($page['page']);  
              
              if( ($pageNumber + 1) > count($pages) ){
                $link = $request->getURI()."?page=".$page['page'];
                  return View::render("layout/pagination/item_right",[
                    'link' => $link,
                    'disabled' => 'disabled'
                  ]);
              }else{
                $link = $request->getURI()."?page=".($pageNumber + 1);
                return View::render("layout/pagination/item_right",[
                  'link' => $link,
                  'disabled' => ''
                ]);
              }
            
            }
        }
    }

    // metodo que renderiza a paginacao
    public static function getPagination($pagination,$request){
        
        $links = '';

        foreach($pagination->getPages() as $page)
        {
            $link = $request->getURI()."?page=".$page['page'];
            
            $links .= View::render("layout/pagination/item",[
                'link' => $link,
                'item' => $page['page'],
                'active' => ($page['current'])? 'active' :''
            ]);
        }
        $prevLinK = self::getPrevLink($pagination->getPages(),$request);
        $nextLink = self::getNextLink($pagination->getPages(),$request);
        
        $allLinks = $prevLinK . " " .$links . " ". $nextLink;
        // $links.= $nextLink;
        return View::render("layout/pagination/box",[
            'links' => $allLinks
        ]);
    }
   
}