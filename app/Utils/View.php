<?php
namespace App\Utils;

/**
 * Classe para busca e renderização das views
 */
class View{
    
    private static $vars;

    public static function init($vars = []){
        self::$vars = $vars;
    }

    private static function getContent($view){
        $file = __DIR__."/../View/".$view.".html";
        return file_exists($file)?file_get_contents($file):"";
    }
    public static function render($view,$vars = []){
        $content = self::getContent($view);

        $vars = array_merge(self::$vars,$vars);
        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return "{{".$item."}}";
        },$keys);

        return str_replace($keys,array_values($vars),$content);
    }
}