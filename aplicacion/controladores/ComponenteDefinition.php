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
class ComponenteDefinition extends Controlador{
    
    /**
     * Devuelve la definicion de un componente 
     */
    public function doGet(){
        //Agarro el nombre desde un parametro GET
        $nombre= $this->parametro_get("nombre");
        if($nombre != NULL){
            echo componente('ui_component', array("nombre" => $nombre));
        }
    }

}

?>
