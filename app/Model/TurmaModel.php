<?php
namespace App\Model;
use App\Utils\Conexao;

class TurmaModel{

    public function getAll($limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';

        $sql = "SELECT * FROM turmas ORDER BY id DESC ".$limit;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }
        
    }
    /**
     * Busca as turmas de determinado usuario
     * @param int $id ID do usuario
     * @return array as turmas
     */
    public function getMyTurmas($id,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';

        $sql = "SELECT * FROM turmas WHERE id_user = :id ".$limit;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }
        
    }
    
    public function getTurmaById($id_turma){
        $sql = "SELECT * FROM turmas WHERE id = :id";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id",$id_turma);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return null;
        }
    }

    /**
     * Metodo que busca uma turma com base na descricao
     * @param string $descricao a descricao da turma
     * @return array|null
     */
    public static function getTurmaByDescricao($descricao,$id_user = null)
    {
        $user = ($id_user != null)?$id_user: '';

        $sql = "SELECT * FROM turmas WHERE descricao = :descricao AND id_user = :user";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":descricao",$descricao);
        $stmt->bindParam(":user",$user);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return null;
        }
    }


    public function getTotalEstudantes($id_turma){
        $sql = "SELECT COUNT(*) as total FROM estudante_na_turma WHERE id_turma = :id_turma";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_turma",$id_turma);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return ['total' => 0];
        }   
    }

    /**
     * Retorna os estudantes de uma certa turma
     */
    public function getEstudantesNaTurma($id_turma,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';

        $sql = "SELECT E.id as id,E.nome as nome,E.email,E.data_de_nascimento as datas from estudante_na_turma ET 
                INNER JOIN estudantes E ON (E.id = ET.id_estudante)
                INNER JOIN turmas T ON(ET.id_turma = T.id) WHERE T.id = :id_turma ORDER BY E.id DESC ".$limit;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_turma",$id_turma);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }   
    }
    

    public function getAllEstudantesNaTurma($limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';

        $sql = "SELECT E.id as id,E.nome as nome,E.email,T.descricao from estudante_na_turma ET 
                INNER JOIN estudantes E ON (E.id = ET.id_estudante)
                INNER JOIN turmas T ON(ET.id_turma = T.id)  order by E.id DESC ".$limit;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":limite",$limit);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }   
    }

    public function getMyEstudantesNaTurma($id_user,$limit = null){
        $limit = strlen($limit) ? 'LIMIT '.$limit : '';

        $sql = "SELECT E.id as id,E.nome as nome,E.email,T.descricao from estudante_na_turma ET 
                INNER JOIN estudantes E ON (E.id = ET.id_estudante)
                INNER JOIN turmas T ON(ET.id_turma = T.id) WHERE T.id_user = :id_user  order by E.id DESC ".$limit;
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_user",$id_user);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }   
    }
    
    public function getEstudanteNaTurma($id_estudante){
        $sql = "SELECT E.id as id,E.nome as nome,E.email,E.data_de_nascimento as datas,T.id as id_turma,T.descricao from estudante_na_turma ET 
                INNER JOIN estudantes E ON (E.id = ET.id_estudante)
                INNER JOIN turmas T ON(ET.id_turma = T.id) WHERE E.id = :id";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id",$id_estudante);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return null;
        }   
    }
    
    public function getEstudanteByEmail($email){
        $sql = "SELECT nome,email from estudantes 
                WHERE email = :email";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":email",$email);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return null;
        }   
    }
    
    public function insert($desc,$id){
        $sql = "INSERT INTO turmas (descricao,id_user) VALUES (:descr,:id_user)";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":descr",$desc);
        $stmt->bindParam(":id_user",$id);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }
        
    }
    public function update($id){
        $sql = "SELECT * FROM turmas WHERE id_user = {$id}";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return null;
        }
        
    }
    public function delete($id){
        $sql = "DELETE FROM turmas WHERE id = {$id}";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }
        
    }

    
    public function removeAllStudentsFromTurma($id_turma){
        $sql = "DELETE FROM estudante_na_turma WHERE id_turma = {$id_turma}";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }
    }

    public function deleteFromTurma($id_aluno,$id_turma){
        $sql = "DELETE FROM estudante_na_turma WHERE id_estudante= {$id_aluno} and id_turma = {$id_turma}";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }
    }


    public function getMyTurmasTotal(int $user){
        $sql = "SELECT COUNT(*) as total FROM turmas WHERE id_user = :id_user";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_user",$user);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return ['total' => 0];
        }   
    }


    public function getMyStudentsTotal(int $user){
        $sql = "SELECT COUNT(*) as total FROM estudantes ES 
                INNER JOIN estudante_na_turma ET ON(ES.id = ET.id_estudante)
                INNER JOIN turmas TU ON (TU.id = ET.id_turma)
                where TU.id_user = :id_user";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_user",$user);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return ['total' => 0];
        }   
    }
    public function getStudentFromTurma($id_estudante,$id_turma)
    {
        $sql = "SELECT id,id_estudante,id_turma FROM estudante_na_turma
                WHERE id_estudante = :id_estudante AND id_turma = :id_turma";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_estudante",$id_estudante);
        $stmt->bindParam(":id_turma",$id_turma);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{
            return null;
        } 
    }

    
    public function insertStudentInTurma($id_estudante,$id_turma){
        $sql = "INSERT INTO estudante_na_turma (id_estudante,id_turma) VALUES (:id_estudante,:id_turma)";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id_estudante",$id_estudante);
        $stmt->bindParam(":id_turma",$id_turma);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }
        
    }

}