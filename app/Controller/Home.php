<?php
namespace App\Controller;

use App\Model\EstudanteModel;
use App\Model\MarcacaoModel;
use App\Model\TurmaModel;
use App\Model\UserModel;
use App\Utils\View;

class Home extends Template{

    /**
     * Metodo responsável por renderizar a pagina principal do sistema
     */
    public static function getHome(){

        $turmaModel = new TurmaModel();
        $estudanteModel = new EstudanteModel();
        $userModel = new UserModel();
        $marcacaoModel = new MarcacaoModel();
        // print_r($_SESSION['usuario']);

        $totalEstudantes = count($turmaModel->getMyEstudantesNaTurma($_SESSION['usuario']['id']));
        $totalTurmas = count($turmaModel->getMyTurmas($_SESSION['usuario']['id']));
        $totalUsers = count($userModel->getAll());
        $presencas = count($marcacaoModel->getMyPresencas($_SESSION['usuario']['id']));
        $faltas = count($marcacaoModel->getMyFaltas($_SESSION['usuario']['id']));
        $totalMarcacoes = count($marcacaoModel->getMarcacoes($_SESSION['usuario']['id']));

        $content = View::render('layout/dashboard',[
            'text1' => "Turmas",
            'value1'=> $totalTurmas,
            'text2' => "Estudantes",
            'value2'=> $totalEstudantes,
            'text3' => ($_SESSION['usuario']['acess']=='admin')?'Utilizadores':"Presencas",
            'value3'=> ($_SESSION['usuario']['acess']=='admin')?$totalUsers:$presencas,
            'text4' => ($_SESSION['usuario']['acess']=='admin')?'Marcações':"Faltas",
            'value4'=> ($_SESSION['usuario']['acess']=='admin')?$totalMarcacoes:$faltas
        ]);

        
        return self::getTemplate("DASHBOARD","Home","Marcação de Presenças",$content);
    }
}