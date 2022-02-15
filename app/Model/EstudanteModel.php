<?php
namespace App\Model;
use App\Utils\Conexao;

class EstudanteModel{

    public function getAllEstudantes($limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';

        $sql = "SELECT * from estudantes order by id DESC ".$limit;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }   
    }
}