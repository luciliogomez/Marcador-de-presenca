<?php
namespace App\Controller;
use App\Model\TurmaModel;
use App\Model\MarcacaoModel;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;
use App\Utils\Alert;
use App\Utils\Conexao;
use App\Utils\LoadPdf;

class Marcacao extends Template{


    /**
     * Metodo responsavel por renderizar a pagina inicial para busca de marcações realizadas
     */
    public static function index($request,$marcacoes_of_today = false)
    {
        $queryParams = $request->getQueryParams();
        $pagination = null;
        $marcacoes = [];
        if($marcacoes_of_today == true || (isset($queryParams['page']))){
            $marcacaoModel = new MarcacaoModel();
            try{

                $marcacoes = $marcacaoModel->getMarcacoesByTurma($_SESSION['turma'],$_SESSION['data']);
                $total = count($marcacoes);


                $queryParams = $request->getQueryParams();
                $page = $queryParams['page']?? '1';

                $pagination = new Pagination($total,$page,4);

                $marcacoes = $marcacaoModel->getMarcacoesByTurma($_SESSION['turma'],$_SESSION['data'],$pagination->getLimit());
            
            }catch(\Exception $ex){
                $erros []="Sem Marcacoes [". $ex->getMessage() ."]";    
                $marcacoes = [];
                $pagination = null;
            }
        }

        $tabela = self::getTabela(["Turma","Id","Nome","Presenca"],
                                    ($marcacoes),
                                        '/marcacao',"id",0,false,false,false
                                    );


        if($pagination == null)
        {
            // echo "NULL";
            $paginacao = ''; 
        }else{
            $paginacao = self::getPagination($pagination,$request);
        }

        // $paginacao = (? : '');
//         echo '<pre>';
// print_r($pagination);
// echo '</pre>';exit;

        $content = View::render('pages/marcacoes/lista',[
            'turmas' => self::getTurmas($_SESSION['usuario']['id']),
            'tabela'    => $tabela,
            'pagination'=> $paginacao,
            'status'     => (isset($queryParams['status']))? self::getStatus($request) : ''
          ]);
        return self::getTemplate("Marcações","Marcações","Marcação de Presença",$content);
    }


    /**
     * Metodo responsavel por buscar marcacoes de uma turma feitas numa determinada data
     * @param object $request a requisicao
     */
    public static function getMarcacoes($request)
    {
        $turmaModel = new TurmaModel();
        $postVars = $request->getPostVars();
        $id_turma = $postVars['turma'];
        $data = $postVars['data'];
        if($id_turma==null || $data==null){
          $request->getRouter()->redirect('/marcacoes?status=empty');
        }

        $turma  = $turmaModel->getTurmaById($id_turma);
        $_SESSION['data']  = $data;
        $_SESSION['turma'] = $id_turma;

        $pagination = null;
        $marcacoes = [];
        $marcacaoModel = new MarcacaoModel();
        try
        {
                $marcacoes = $marcacaoModel->getMarcacoesByTurma($id_turma,$data);
                $total = count($marcacoes);

                $queryParams = $request->getQueryParams();
                $page = $queryParams['page']?? '1';
                
                $pagination = new Pagination($total,$page,4);
                $marcacoes = $marcacaoModel->getMarcacoesByTurma($id_turma,$data,$pagination->getLimit());
        }catch(\Exception $ex)
        {   
            $marcacoes = [];
            $pagination = null;
              
        }
          $tabela = self::getTabela(["Turma","Id","Nome","Presenca"],
                                    ($marcacoes),'/marcacao',"id",false,false,false);
        if(empty($marcacoes)){
            $tabela = Alert::getError("Sem Marcacoes da turma ".$turma['descricao']." nessa data!");
        }
        $content = View::render('pages/marcacoes/lista',[
            'turmas'     => self::getTurmas($_SESSION['usuario']['id']),
            'tabela'     => $tabela,
            'pagination' => ($pagination)?self::getPagination($pagination,$request): 'vazio',
            'status'     =>  ''
          ]);
        return self::getTemplate("Marcações","Marcações","Marcação de Presença",$content);
    }



    /**
     * Metodo responsavel por renderizar a pagina de cadastro de novas marcacoes
     * @param object $request a requisicao
     */
    public static function getNewMarcacao($request)
    {
        $queryParams = $request->getQueryParams();
        $marcacaoModel = new MarcacaoModel();
        $resultados = '';
        if(isset($queryParams['page']))
        {
            $id_turma = $_SESSION['turma'];
            try
            {
                    $estudantes = $marcacaoModel->getEstudantesSemMarcacoesByTurma($id_turma,date('Y-m-d'),$_SESSION['usuario']['id']);
                    $total = count($estudantes);
    
                    $queryParams = $request->getQueryParams();
                    $page = $queryParams['page']?? '1';
                    
                    $pagination = new Pagination($total,$page,4);
                    $estudantes = $marcacaoModel->getEstudantesSemMarcacoesByTurma($id_turma,date('Y-m-d'),$_SESSION['usuario']['id'],$pagination->getLimit());
                    
                }catch(\Exception $ex)
            {   
                $estudantes = [];
                $pagination = null;
                  
            }
              $tabela = self::getTabela(["Id","Nome","Presenca"],
                                        ($estudantes),'/marcacao',"id",$id_turma,false,false,false,true,true); 
        
            $resultados = $tabela;
        }


        $content = View::render('pages/marcacoes/nova',[
            'turmas'     => self::getTurmas($_SESSION['usuario']['id']),
            'resultados' => $resultados,
            'pagination' => ($resultados!='')?self::getPagination($pagination,$request):'',
            'status' => (isset($queryParams['status'])?self::getStatus($request):'')
          ]);

        return self::getTemplate("Marcações","Marcações","Nova Marcação",$content);
    }

    /**
     * Metodo responsavel por buscar estudantes sem marcacao em certa turma e no dia corrente
     * @param object $request a requisicao
     */
    public static function getEstudantesParaMarcacao($request)
    {

        $postVars = $request->getPostVars();
        $id_turma = $postVars['turma'];
        $estudantes = [];
        $resultados = null;
        $marcacaoModel = new MarcacaoModel();
        try
        {
                $estudantes = $marcacaoModel->getEstudantesSemMarcacoesByTurma($id_turma,date('Y-m-d'),$_SESSION['usuario']['id']);
                $total = count($estudantes);

                $queryParams = $request->getQueryParams();
                $page = $queryParams['page']?? '1';
                
                $pagination = new Pagination($total,$page,4);
                $estudantes = $marcacaoModel->getEstudantesSemMarcacoesByTurma($id_turma,date('Y-m-d'),$_SESSION['usuario']['id'],$pagination->getLimit());
                $_SESSION['turma'] = $id_turma;
            }catch(\Exception $ex)
        {   
            $estudantes = [];
            $pagination = null;
              
        }
          $tabela = self::getTabela(["Id","Nome","Presenca"],
                                    ($estudantes),'/marcacao',"id",$id_turma,false,false,false,true,true);
        if(!empty($estudantes)){
            $resultados = $tabela;
        }else{
            $resultados = Alert::getError("Não Há Estudantes sem Marcação Hoje!");
        }

//         echo '<pre>';
// print_r($estudantes);
// echo '</pre>';exit;
        $content = View::render('pages/marcacoes/nova',[
            'turmas'     => self::getTurmas($_SESSION['usuario']['id']),
            'resultados' => $resultados,
            'pagination' => self::getPagination($pagination,$request),
            'status'     => ''
        ]);

        return self::getTemplate("Marcações","Marcações","Nova Marcação",$content);
    }



    /**
     * Metodo responsavel por cadastrar uma nova marcacao
     * @param Object $request a requisicao
     * @param int $idTurma O ID DA TURMA
     * @param int $idEstudante o ID DO ESTUDANTE
     * @param int $estado O ESTADO DA MARCACAO [1-PRESENTE | 0- AUSENTE]
     */
    public static function marcar($request,$idTurma,$idEstudante,$estado)
    {
        $conn = Conexao::getInstance();
        try{
            $conn->beginTransaction();
    
            $sql = "INSERT INTO marcacoes (id_turma,estado)  VALUES  (:id_turma,:estado);";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id_turma",$idTurma);
            $stmt->bindParam(":estado",$estado);
            $stmt->execute();
    
            $id_marcacao = $conn->lastInsertId();
            
            $sql = "INSERT INTO marcacao_estudante (id_marcacao,id_estudante)  VALUES  (:id_marcacao,:id_estudante);";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id_marcacao",$id_marcacao);
            $stmt->bindParam(":id_estudante",$idEstudante);
            $stmt->execute();
    
            $conn->commit();

            $link = "/nova-marcacao?page=1&status=".(($estado==1)?"presente":"ausente");
            
            $request->getRouter()->redirect($link);
    
        }catch(\Exception $ex){
            $conn->rollback();
            $request->getRouter()->redirect('/nova-marcacao?page=1&status=failed');
        }


    }

    /**
     * Metodo que renderiza a pagina de geracao de relatorios
     * @param object $request a requisicao
     */
    public static function getRelatorio($request)
    {
        $content = View::render('pages/marcacoes/relatorio',[
            'turmas'     => self::getAllTurmasRelatorio($_SESSION['usuario']['id']),
            'status'     => self::getStatus($request)
        ]);

        return self::getTemplate("Marcações","Marcações","Gerar Relatório de Faltas",$content);
        

    }

    /**
     * Metodo responsavel por gerar o relatorio de todas as marcacoes feitas num periodo especifico
     * @param object $request a requisicao
     */
    public static function gerarRelatorio($request)
    {
        $loadPdf = new LoadPdf();
        $marcacaoModel = new MarcacaoModel();
        $postVars = $request->getPostVars();
        if(empty($postVars['turma']) || empty($postVars['inicio']) || empty($postVars['fim']))
        {
            $request->getRouter()->redirect("/relatorio?status=empty");
        }

        $turma = $postVars['turma'];
        $inicio = $postVars['inicio'];
        $fim = $postVars['fim'];

        if($turma == 'todas'){
            $estudantes = $marcacaoModel->getRelatorio($_SESSION['usuario']['id'],$inicio,$fim);
            $loadPdf->loadTable("RELATORIO DE FALTAS <br/> de {$inicio} à {$fim}",["NOME","TURMA","FALTAS"],$estudantes);
            $loadPdf->print();
        }else{
            $estudantes = $marcacaoModel->getRelatorioByTurma($_SESSION['usuario']['id'],$turma,$inicio,$fim);
            $loadPdf->loadTable("RELATORIO DE FALTAS <br/> de {$inicio} à {$fim}",["NOME","TURMA","FALTAS"],$estudantes);
            $loadPdf->print();
        }

        $request->getRouter()->redirect("/relatorio?status=generated");
    }



    // Metodo responsavel por renderizar as turmas em um select com uma opcao de "TODAS
    public static function getAllTurmasRelatorio($id_user = null)
    {
        
        $turmaModel = new TurmaModel();
        $turmas = ($id_user == null)? $turmaModel->getAll() : $turmaModel->getMyTurmas($id_user);

        array_unshift($turmas,[
            'id'        => 'todas',
            'descricao' => 'Todas'
        ]);
        
        return self::getSelectInput($turmas,'turma','Escolha a Turma','id','descricao');
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
    public static function getTabela($headers = [],$content = [],$uri = null,$position = "id",$turma=0,$view=false,$edit=false,$delete=false,$presente=false,$ausente=false,$bloq=false){
        
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

     // METODO RESPONSAVEL POR RENDERIZAR (Juntar) as colunas de uma certa linha
     public static function getColunas($colunas = [])
     {
         $cols = '';
         $i = 0;
         foreach($colunas as $item){
             $i++;
             if($i == 4){
                   $cols .= View::render("layout/tabela/coluna",[
                     'item' => ($item == '1')?View::render("pages/marcacoes/presente") :View::render("pages/marcacoes/ausente")
                   ]);
 
             }else{
 
                 $cols .= View::render("layout/tabela/coluna",[
                   'item' => $item
                 ]);
             }
         }
         return $cols;
     }
 
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
            case 'empty':
                  return Alert::getError("Preencha todos os campos!");
                  break;
            case 'presente':
              return Alert::getSucess("Estudante Marcado como PRESENTE!");
              break;
            case 'ausente':
              return Alert::getSucess("Estudante Marcado como AUSENTE!");
              break;
            case 'generated':
                return Alert::getSucess("Relatorio Gerado!");
                break;
        }
        
    }




}