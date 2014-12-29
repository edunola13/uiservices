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
        //Agarro el nombre desde un parametro GET
        $nombre= $this->request->param_get("nombre");
        if($nombre != NULL){
            echo component('ui_component', array("nombre" => $nombre));
        }
    }

}

?>
