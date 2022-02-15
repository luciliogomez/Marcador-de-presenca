<?php
namespace App\Controller;
use App\Model\TurmaModel;
use App\Model\EstudanteModel;
use App\Utils\View;
use App\Model\Estudante as Student;
use App\Utils\Alert;
use App\Utils\Conexao;
use \Exception;
use WilliamCosta\DatabaseManager\Pagination;
class Turma extends Template{

    
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


    /**
     * renderiza a Pagina com a listagem de todas as turmas
     */
    public static function index($request){
        $turmaModel = new TurmaModel();
        $id_user = (int) $_SESSION['usuario']['id'];
        
        $total = count($turmaModel->getMyTurmas($id_user));
          
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page']?? '1';
        
        $pagination = new Pagination($total,$page,4);

        $turmas = $turmaModel->getMyTurmas($id_user);
        $turmas = self::includeTotalStudents($turmas);
        $tabela = self::getTabela(["ID","Descricao","Estudantes","Accoes"],
                                    ($turmas),
                                        '/turma',"id",0,true,false,true
                                    );

        $content = View::render("pages/turmas/lista",[
            'tabela' => $tabela,
            'pagination' => '' 
            // self::getPagination($pagination,$request)
        ]);

        return self::getTemplate("Turmas","Turmas","Minhas Turmas",$content);
    }
    
    private static function includeTotalStudents($turmas){
        $turmaModel = new TurmaModel;
        $id_user = (int) $_SESSION['usuario']['id'];

        foreach($turmas as $key => $value){
            $total = count($turmaModel->getEstudantesNaTurma($turmas[$key]['id']));
            $turmas[$key]['estudantes'] = $total;
            unset($turmas[$key]['id_user']);
        }
        return $turmas;
    }

    private static function getStatus($request)
    {
        $queryParams = $request->getQueryParams();
        if(!isset($queryParams['status'])) return '';

        switch($queryParams['status'])
        {
            case 'created':
                return Alert::getSucess("Turma Adicionada com sucesso");
                break;
            case 'deleted':
                return Alert::getSucess("Turma Eliminada com sucesso");
                break;
            case 'duplicated':
                return Alert::getError("Já existe uma Turma com esse nome");
                break;
            case 'failed':
                return Alert::getError("Ocorreu Um Erro!");
                break;
            case 'dup_student':
                return Alert::getError("Estudante Já Faz parte desta turma!");
                break;
            case 'added_student':
                return Alert::getSucess("Estudante Adicionado!");
                break;
        }
        
    }

    /**
     * Metodo responsavel por renderizar o formulario para cadastro de Turma
     */
    public static function getNewTurma($request)
    {
        $content = View::render("pages/turmas/novo",[
            'status'    => self::getStatus($request),
            'descricao' => ''
        ]);
        return self::getTemplate("Turmas","Turma","ADICIONAR TURMA",$content);
    }

    /**
     * Metodo responsavel por  cadastrar Turma
     */
    public static function setNewTurma($request)
    {

        $postVars = $request->getPostVars();
        $descricao = $postVars['descricao'] ?? '';
        
        $turmaModel = new TurmaModel();

        // VALIDANDO A DESCRICAO VAZIA
        if(empty($descricao)){
            $content = View::render("pages/turmas/novo",[
                'status'    => Alert::getError('Digite a descricao da turma'),
                'descricao' => ''
            ]);
            return self::getTemplate("Turmas","Turma","ADICIONAR TURMA",$content);
        }

        // VALIDANDO A DUPLICACAO DA DESCRICAO 
        $turma = $turmaModel->getTurmaByDescricao($descricao,$_SESSION['usuario']['id']);

        if($turma != null){
            $request->getRouter()->redirect('/nova-turma?status=duplicated');
        }

        try {
            if($turmaModel->insert($descricao,$_SESSION['usuario']['id']))
            {
                $request->getRouter()->redirect('/nova-turma?status=created');
            }else{
                $request->getRouter()->redirect('/nova-turma?status=failed');
            }

        } catch (\Exception $ex) {
            $content = View::render("pages/turmas/novo",[
                'status'    => Alert::getError('Ocorreu um erro. Tente Novamente!'),
                'descricao' => $descricao
            ]);
            return self::getTemplate("Turmas","Turma","ADICIONAR TURMA",$content);
        }

    }



    // Metodo responsavel por renderizar as turmas em um select
    public static function getTurmas($id_user = null)
    {
        $turmaModel = new TurmaModel;
        $turmas = ($id_user == null)? $turmaModel->getAll() : $turmaModel->getMyTurmas($id_user);

        
        return self::getSelectInput($turmas,'turma','Escolha a Turma','id','descricao');
    }


    /**
     * Renderiza a pagina de confirmação para eliminação de turma
     */
    public static function getDeleteTurma($request,$id_turma)
    {
        $turmaModel = new TurmaModel();

        $turma = $turmaModel->getTurmaById($id_turma);
        if($turma == null)
        {
            $request->getRouter()->redirect('/turmas?status=error');
        }

        $content = View::render('pages/turmas/delete',[
            'descricao'    => $turma['descricao'] ?? '',
            'id_turma'     => $turma['id_turma'] ?? '',
            'erro'         => ''
        ]);

        return self::getTemplate("Turmas","Turma","ELIMINAR TURMA",$content);
    }

    /**
     *  Metodo responsavel por remover uma turma
     * @param object $request a requisicao
     * @param int $id_turma O ID da turma
     */
    public static function setDeleteTurma($request,$id_turma)
    {
        $turmaModel = new TurmaModel();

        $turma = $turmaModel->getTurmaById($id_turma);
        if($turma == null)
        {
            $request->getRouter()->redirect('/turmas?status=error');
        }

        try {
            $turmaModel->delete($id_turma);
            $content = View::render("layout/result",[
                'message' => "Turma eliminada!",
                'color'   => 'green'
            ]);
            return self::getTemplate("Turmas","Resutados","Resultados",$content);
        } catch (\Exception $ex) {
            $content = View::render("layout/result",[
                'message' => "Turma Não eliminada!",
                'color'   => 'red'
            ]);
            return self::getTemplate("Turmas","Resutados","Resultados",$content);
            //throw $th;
        }
        $content = View::render('pages/turmas/delete',[
            'descricao'    => $turma['descricao'] ?? '',
            'id_turma'     => $turma['id_turma'] ?? '',
            'erro'         => ''
        ]);

        return self::getTemplate("Turmas","Turma","ELIMINAR TURMA",$content);
    }

    /**
     * renderiza a pgina com a lista de todos estudantes que fazem parte de certa turma
     * @param object $request a requisicao
     * @param int $id_turma O ID da turma
     */
    public static function getViewTurma($request,$id_turma){
        $turmaModel = new TurmaModel();
        $id_user = (int) $_SESSION['usuario']['id'];
        $turma = $turmaModel->getTurmaById($id_turma);

        $total = count($turmaModel->getEstudantesNaTurma($id_turma));
          
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page']?? '1';
        
        $pagination = new Pagination($total,$page,4);

        $estudantes = $turmaModel->getEstudantesNaTurma($id_turma,$pagination->getLimit());
        $tabela = self::getTabela(["ID","Nome","Email","Data de Nascimento","Accoes"],
                                    ($estudantes),
                                        '/estudante',"id",0,false,false,true
                                    );

        $content = View::render("pages/turmas/lista_de_estudantes",[
            'tabela'     => $tabela,
            'pagination' => self::getPagination($pagination,$request),
            'turma'      => $id_turma
        ]);

        return self::getTemplate("Turmas","Turmas","Estudantes da Turma - ".$turma['descricao'],$content);
    }


    /**
     * Metodo querenderiza a Pagina com lista de estudantes para adicionar em uma turma
     */
    public static function getAddEstudantesNaTurma($request,$id_turma){
        $estudanteModel = new EstudanteModel;
        $turmaModel = new TurmaModel();

        $id_user = (int) $_SESSION['usuario']['id'];
        $turma = $turmaModel->getTurmaById($id_turma);

        $total = count($estudanteModel->getAllEstudantes());
          
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page']?? '1';
        
        $pagination = new Pagination($total,$page,4);

        $estudantes = $estudanteModel->getAllEstudantes($pagination->getLimit());
        $tabela = self::getTabela(["ID","Nome","Email","Data de Nascimento","Acção"],
                                    ($estudantes),
                                        '/add-estudante',"id",$id_turma,false,false,false,false,false,true
                                    );

        $content = View::render("pages/turmas/lista_de_estudantes_add",[
            'tabela'     => $tabela,
            'pagination' => self::getPagination($pagination,$request),
            'turma'      => $id_turma,
            'status'     => self::getStatus($request)
        ]);

        return self::getTemplate("Turmas","Turmas","Adicionar Estudantes na Turma - ".$turma['descricao'],$content);
    }

      /**
     * Metodo responsavel por incluir um estudante [ja existente] numa certa turma
     * @param object $request a requisicao
     * @param int $idEstudante O ID DO ESTUDANTE
     * @param int $idTurma O ID DA TURMA
     */
    public static function getIncluirEstudanteNaTurma($request,$idEstudante,$idTurma)
    {
        $turmaModel = new TurmaModel();
        // VALIDAR DUPLICACAO DE ESTUDANTE NA TURMA
        $estudante = $turmaModel->getStudentFromTurma($idEstudante,$idTurma);
        if($estudante != null){
            $request->getRouter()->redirect("/adicionar-estudante-turma-{$idTurma}?status=dup_student");
        }

        try{
            if($turmaModel->insertStudentInTurma($idEstudante,$idTurma)){
                $request->getRouter()->redirect("/adicionar-estudante-turma-{$idTurma}?status=added_student");
            }else{
                $request->getRouter()->redirect("/adicionar-estudante-turma-{$idTurma}?status=failed");
            }
        }catch(\Exception $ex)
        {
            $request->getRouter()->redirect("/adicionar-estudante-turma-{$idTurma}?status=failed");
        }

    }

        /**
     * Metodo responsavel por renderizar uma tabela
     * @param array $header os titulos para o cabecalho
     * @param array $content o as linhas da tabela, precisa ser uma matriz ou array associativo
     * @param string $uri a URI para os links em botoes de opção de view, editar ou delete
     * @param string $position a posicao do item que vai servir de complemento para a URI [o ID por exemplo]
     * @param bool $view define se vai ter botao de view
     * @param bool $edit define se vai ter botao de edit
     * @param bool $delete define se vai ter botao de delete
     */
    public static function getTabela($headers = [],$content = [],$uri = null,$position = "id",$turma=0,$view=false,$edit=false,$delete=false,$presente=false,$ausente=false,$add=false){
        
        $cabecalho = self::getCabecalho($headers);


        $linhas = '';

        foreach($content as $linha => $colunas){
            $cols = self::getColunas($colunas);

            if(isset($uri)){
                $botoes = '';
                if($view){

                    $botoes.= View::render("layout/tabela/botao_view",[
                        'uri'    => $uri,
                        'target' => $colunas[$position]
                    ]);
                }

                if($edit){
                    $botoes.= View::render("layout/tabela/botao_edit",[
                        'uri'    => $uri,
                        'target' => $colunas[$position]
                    ]);
                }

                if($delete){
                    $botoes.= View::render("layout/tabela/botao_delete",[
                        'uri'    => $uri,
                        'target' => $colunas[$position]
                    ]);
                }
                if($presente){
                    $botoes.= View::render("layout/tabela/botao_presente",[
                        'uri'       => $uri,
                        'estudante' => $colunas[$position],
                        'turma'     => $turma
                    ]);
                }
                if($ausente){
                    $botoes.= View::render("layout/tabela/botao_ausente",[
                        'uri'       => $uri,
                        'estudante' => $colunas[$position],
                        'turma'     => $turma
                    ]);
                }
                if($add){
                    $botoes.= View::render("layout/tabela/botao_add_student_to_turma",[
                        'uri'       => $uri,
                        'estudante' => $colunas[$position],
                        'turma'     => $turma
                    ]);
                }
            
                $cols .= View::render("layout/tabela/coluna",[
                    'item' => $botoes
                ]);
            }

            $linhas .= View::render("layout/tabela/linha",[
                'colunas' => $cols
            ]);
        }


        return View::render("layout/tabela/table",[
            'cabecalho' =>$cabecalho,
            'linhas' => $linhas
        ]);
    }
   
}