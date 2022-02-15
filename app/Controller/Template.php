<?php
namespace App\Controller;

use App\Utils\View;
use App\Model\TurmaModel;
use App\Model\UserModel;

class Template{
    
    /**
     * Metodo responsavel por renderizar o menu de navegação
     */
    public static function getNavbar(){
        return View::render('layout/navbar',[
            'URL' => getenv("URL"),
            'usuarios' => ($_SESSION['usuario']['acess'] == 'admin')?View::render('layout/users_link'):''
        ]);
    }

    /**
     * Metodo responsavel por renderizar o template do sistema com o conteudo solicitado
     * @param string $title o titulo da Pagina
     * @param string $top indica o modulo actual
     * @param string $description pequeno texto indicativo da pagina actual
     * @param string $content o conteudo da pagina actual
     */
    public static function getTemplate($title,$top,$description,$content){

        return View::render("layout/template",[
            'title'      =>  $title,
            'navbar'     =>  self::getNavbar(),
            'top'        =>  $top,
            'description'=>  $description,
            'content'    => $content
            
        ]);

    }

    /**
     * Metodo responsavel por renderizar uma o cabecalho da tabela
     * @param array $header os titulos para o cabecalho
     */
    public static function getCabecalho($headers = [])
    {
        $titulos = '';

        foreach($headers as $header){
            $titulos .= View::render("layout/tabela/titulo",[
                'titulo' => $header
            ]);
        }

        return    View::render("layout/tabela/cabecalho",[
                    'titulos' => $titulos
                    ]);
    }


    // METODO RESPONSAVEL POR RENDERIZAR (Juntar) as colunas de uma certa linha
    public static function getColunas($colunas = [])
    {
        $cols = '';

        foreach($colunas as $item){
            $cols .= View::render("layout/tabela/coluna",[
                'item' => $item
            ]);
        }
        return $cols;
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
    public static function getTabela($headers = [],$content = [],$uri = null,$position = "id",$turma=0,
                        $view=false,$edit=false,$delete=false,$presente=false,$ausente=false,$bloq_user=false){
        
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

                if($bloq_user){
                    $userModel = new UserModel();
                    $usuario = $userModel->getUserById($colunas[$position]);
                    if($usuario['status'] == 1){

                        $botoes.= View::render("layout/tabela/botao_bloquear",[
                            'uri'       => $uri,
                            'idUsuario' => $colunas[$position]
                        ]);
                    }else{
                        $botoes.= View::render("layout/tabela/botao_desbloquear",[
                            'uri'       => $uri,
                            'idUsuario' => $colunas[$position]
                        ]);
                    }
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

     /**
     * Metodo responsavel por renderizar um input do tipo Select
     * @param array $values o array de valores que fazem parte do select [precisa ser uma matriz ou array associativo]
     * @param string $name o valor do atributo 'name' do select
     * @param string $top um texto para o topo do select como primeiro valor[disable] do select
     * @param string $value a chave do array que servira como valor do atributo value nas options do select
     * @param string $text a chave do array que servira como o texto visivel nas options do select 
     */
    public static function getSelectInput($values = [],$name,$top,$value = null,$text = null)
    {
        $options = '';

        foreach($values as $item => $subitens){
            $options .= View::render("layout/select/option",[
                'text'  => $subitens[$text],
                'value' => $subitens[$value]
            ]);
        }

        return View::render('layout/select/select',[
            'name'    =>$name,
            'top'     => $top,
            'options' =>$options
        ]);
    }

    // Metodo responsavel por renderizar as turmas em um select
    public static function getTurmas($id_user = null)
    {
        
        $turmaModel = new TurmaModel();
        $turmas = ($id_user == null)? $turmaModel->getAll() : $turmaModel->getMyTurmas($id_user);

        
        return self::getSelectInput($turmas,'turma','Escolha a Turma','id','descricao');
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


}