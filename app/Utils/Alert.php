<?php
namespace App\Utils;

use App\Utils\View;


/**
 * Uma classe para exibição de alertas [mensagens de sucesso ou de erros]
 */

class Alert{

    /**
     * Metodo responsavel por exibir uma mensagem de erro
     * @param string $message a mensagem
     */
    public static function getError($message){

        return View::render('layout/alert',[
            'color'   => 'red',
            'message' => $message
        ]);
    }
    

    /**
     * Metodo responsavel por exibir uma mensagem de Sucesso
     * @param string $message a mensagem
     */
    public static function getSucess($message){

        return View::render('layout/alert',[
            'color'   => 'green',
            'message' => $message
        ]);
    }
}