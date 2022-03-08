<?php
namespace App\Model;
use App\Utils\Conexao;


class MarcacaoModel{


    /**
     * busca todas as marcacoes de presencas de um usuario numa data
     * @param string $data a Data da marcacao
     * @param int $user o ID do Usuario
     * @return array todas as marcacoes
     */
    public function getAllMarcacoes(string $data,int $user,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT TU.descricao as turma,ES.id, ES.nome,MA.estado FROM marcacoes MA
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id)
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id)
                INNER JOIN turmas TU ON (TU.id = MA.id_turma)
                WHERE MA.data = :data AND TU.id_user = :user ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":data",$data);
        $stmt->bindParam(":user",$user);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }

    /**
     * busca todas as marcacoes de presecas de uma determinada turma num data
     * @param int $turma o ID da turma
     * @param string $data a Data da marcacao
     * @return array todas as marcacoes
     */
    public function getMarcacoesByTurma(int $turma,string $data,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT TU.descricao as turma,ES.id, ES.nome,MA.estado FROM marcacoes MA
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id)
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id)
                INNER JOIN turmas TU ON (TU.id = MA.id_turma)
                WHERE TU.id = :id_turma AND MA.data = :data  order by TU.id DESC ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_turma",$turma);
        $stmt->bindParam(":data",$data);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }

    /**
     * busca estudantes para nova marcacao.
     * Busca estudantes que ainda nao foram marcados como presente ou ausente numa determinada data. 
     * @param int $turma o ID da turma
     * @param string $data a Data da marcacao
     * @param int $user o ID do usuario logado
     * @return array todos os estudantes sem marcacao nessa data
     */
    public function getEstudantesSemMarcacoesByTurma(int $turma,string $data,int $user,$limit = null){
        
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT id, nome FROM estudantes 
                WHERE id IN 
                (SELECT id_estudante FROM estudante_na_turma ET 
                INNER JOIN turmas TUU ON (TUU.id = ET.id_turma) 
                WHERE TUU.id_user = :user AND id_turma = :turma) 
                AND id NOT IN(SELECT ES.id FROM marcacoes MA 
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id) 
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id) 
                INNER JOIN turmas TU ON (TU.id = MA.id_turma) 
                WHERE TU.id = :turma AND MA.data = :data 
                AND TU.id_user = :user )  order by id DESC ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":turma",$turma);
        $stmt->bindParam(":data",$data);
        $stmt->bindParam(":user",$user);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }

    /**
     * busca todos estudantes para nova marcacao.
     * Busca todos estudantes que ainda nao foram marcados como presente ou ausente numa determinada data. 
     * @param int $turma o ID da turma
     * @param string $data a Data da marcacao
     * @return array todos os estudantes sem marcacao nessa data
     */
    public function getAllEstudantesSemMarcacoesByTurma(int $turma,string $data,$limit = null){
        
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT id, nome FROM estudantes 
                WHERE id IN 
                (SELECT id_estudante FROM estudante_na_turma WHERE id_turma = :turma) 
                AND id NOT IN(SELECT ES.id FROM marcacoes MA 
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id) 
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id) 
                INNER JOIN turmas TU ON (TU.id = MA.id_turma) 
                WHERE TU.id = :turma AND MA.data = :data )  order by id DESC ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":turma",$turma);
        $stmt->bindParam(":data",$data);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }


    /**
     * Busca o total de faltas de cada aluno durante um periodo.
     * @param string $start a Data inicial
     * @param string $end a Data Final
     * @param int $user o ID do usuario logado
     * @return array todos os estudantes com suas quantidades de falta
     */
    public function getRelatorio(string $start,string $end = HOJE){

        $sql = "SELECT ES.nome,TU.descricao as turma,count(MA.estado) as faltas FROM estudantes ES
                INNER JOIN marcacao_estudante ME ON (ME.id_estudante = ES.id)
                INNER JOIN marcacoes MA ON (MA.id = ME.id_marcacao)
                INNER JOIN turmas TU ON (MA.id_turma = TU.id)
                WHERE (MA.estado = 0) AND MA.data BETWEEN :start  AND :end
                GROUP BY ES.nome , TU.descricao ";
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        
        $stmt->bindParam(":start",$start);
        $stmt->bindParam(":end",$end);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }
    /**
     * Busca o total de faltas de cada aluno de um Usuario durante um periodo.
     * @param string $start a Data inicial
     * @param string $end a Data Final
     * @param int $user o ID do usuario logado
     * @return array todos os estudantes com suas quantidades de falta
     */
    public function getMyRelatorio(int $user,string $start,string $end = HOJE){

        $sql = "SELECT ES.nome,TU.descricao as turma,count(MA.estado) as faltas FROM estudantes ES
                INNER JOIN marcacao_estudante ME ON (ME.id_estudante = ES.id)
                INNER JOIN marcacoes MA ON (MA.id = ME.id_marcacao)
                INNER JOIN turmas TU ON (MA.id_turma = TU.id)
                WHERE (MA.estado = 0 AND TU.id_user = :user) AND MA.data BETWEEN :start  AND :end
                GROUP BY ES.nome , TU.descricao ";
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":user",$user);
        $stmt->bindParam(":start",$start);
        $stmt->bindParam(":end",$end);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }

    public function getRelatorioByTurma(int $turma,string $start,string $end = HOJE){

        $sql = "SELECT ES.nome,TU.descricao as turma,count(MA.estado) as faltas FROM estudantes ES
                INNER JOIN marcacao_estudante ME ON (ME.id_estudante = ES.id)
                INNER JOIN marcacoes MA ON (MA.id = ME.id_marcacao)
                INNER JOIN turmas TU ON (MA.id_turma = TU.id)
                WHERE (MA.estado = 0  AND MA.id_turma = :turma) AND MA.data BETWEEN :start  AND :end
                GROUP BY ES.nome , TU.descricao ";
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
       
        $stmt->bindParam(":turma",$turma);
        $stmt->bindParam(":start",$start);
        $stmt->bindParam(":end",$end);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }


    public function getMyRelatorioByTurma(int $user,int $turma,string $start,string $end = HOJE){

        $sql = "SELECT ES.nome,TU.descricao as turma,count(MA.estado) as faltas FROM estudantes ES
                INNER JOIN marcacao_estudante ME ON (ME.id_estudante = ES.id)
                INNER JOIN marcacoes MA ON (MA.id = ME.id_marcacao)
                INNER JOIN turmas TU ON (MA.id_turma = TU.id)
                WHERE (MA.estado = 0 AND TU.id_user = :user AND MA.id_turma = :turma) AND MA.data BETWEEN :start  AND :end
                GROUP BY ES.nome , TU.descricao ";
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":user",$user);
        $stmt->bindParam(":turma",$turma);
        $stmt->bindParam(":start",$start);
        $stmt->bindParam(":end",$end);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }


    public function getMyPresencas(int $user,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT TU.descricao as turma,ES.id, ES.nome,MA.estado FROM marcacoes MA
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id)
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id)
                INNER JOIN turmas TU ON (TU.id = MA.id_turma)
                WHERE TU.id_user = :user AND MA.estado=1".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":user",$user);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }

    public function getMyFaltas(int $user,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT TU.descricao as turma,ES.id, ES.nome,MA.estado FROM marcacoes MA
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id)
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id)
                INNER JOIN turmas TU ON (TU.id = MA.id_turma)
                WHERE TU.id_user = :user AND MA.estado=0 ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":user",$user);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }

       /**
     * busca todas as marcacoes de presencas de um usuario
     * @param int $user o ID do Usuario
     * @return array todas as marcacoes
     */
    public function getMarcacoes(int $user,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT TU.descricao as turma,ES.id, ES.nome,MA.estado FROM marcacoes MA
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id)
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id)
                INNER JOIN turmas TU ON (TU.id = MA.id_turma)
                WHERE TU.id_user = :user ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":user",$user);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }
      /**
     * busca todas as marcacoes de presencas sem levar em consideracao o Usuario que marcou
     * @return array todas as marcacoes
     */
    public function getTodasMarcacoes($limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';
        $sql = "SELECT TU.descricao as turma,ES.id, ES.nome,MA.estado FROM marcacoes MA
                INNER JOIN marcacao_estudante ME ON (ME.id_marcacao = MA.id)
                INNER JOIN estudantes ES ON (ME.id_estudante = ES.id)
                INNER JOIN turmas TU ON (TU.id = MA.id_turma) ".$limit;
        ;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>0){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else{
            return [];
        }
    }


}