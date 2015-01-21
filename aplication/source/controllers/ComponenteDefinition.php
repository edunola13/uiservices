<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Prueba
 *
 * @author Usuario_2
 */
class ComponenteDefinition extends En_Controller{    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Devuelve la definicion de un componente 
     */
    public function doGet(){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        //Agarro el nombre desde un parametro GET
        $nombre= $this->request->param_get("nombre");
        if($nombre != NULL){
            echo component('ui_component', array("nombre" => $nombre));
        }
    }

}

?>
