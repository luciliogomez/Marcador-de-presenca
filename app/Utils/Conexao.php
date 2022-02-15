<?php
namespace App\Utils;
use \PDO;

/**
 * Classe de Conexao com a base de dados
 */
class Conexao
{
    private static $instance;

    public static function getInstance()
    {
        if(!isset(self::$instance))
        {
            self::$instance = new \PDO(
                "mysql:host=" . getenv('DB_HOST') . ";
                dbname=" . getenv('DB_NAME') .";charset=utf8",
                getenv('DB_USER'),getenv('DB_PASSWORD')
            );
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    } 
}