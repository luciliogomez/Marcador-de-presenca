<?php
namespace App\Model;
use App\Utils\Conexao;
use PDO;
class UserModel{

    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM utilizadores WHERE email = :email";

        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":email",$email);
        $stmt->execute();

        if($stmt->rowCount() == 1)
        {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function getAll($limit = null)
    {
        $limit = strlen($limit)? " LIMIT ".$limit :'';

        $sql = "SELECT id,name,email,acess FROM utilizadores ".$limit;

        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() >= 1)
        {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function getUserById($id)
    {
        $sql = "SELECT id,name,email,status,acess FROM utilizadores WHERE id = :id";

        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        if($stmt->rowCount() == 1)
        {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function insert($nome,$email,$acesso){
        $sql = "INSERT INTO utilizadores (name,email,acess,password,status) 
        VALUES (:nome,:email,:acess,:password,1)";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":nome",$nome);
        $stmt->bindParam(":email",$email);
        $stmt->bindParam(":acess",$acesso);
        $stmt->bindParam(":password",(password_hash("12345",PASSWORD_DEFAULT)));
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }
    }
    public function bloquerUtilizador($idUsuario)
    {
        $sql = "UPDATE utilizadores SET status=0 WHERE id=:idUsuario";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":idUsuario",$idUsuario);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }

    }
    public function desbloquerUtilizador($idUsuario)
    {
        $sql = "UPDATE utilizadores SET status=1 WHERE id=:idUsuario";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":idUsuario",$idUsuario);
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }

    }

    public function alterarSenha($idUsuario,$senha)
    {
        $sql = "UPDATE utilizadores SET password = :senha WHERE id = :idUsuario";
        $stmt = Conexao::getInstance()->prepare($sql);
        $stmt->bindParam(":idUsuario",$idUsuario);
        $stmt->bindParam(":senha",password_hash($senha,PASSWORD_DEFAULT));
        $stmt->execute();
        if($stmt->rowCount()>=1){
            return true;
        }else{
            return false;
        }

    }
}