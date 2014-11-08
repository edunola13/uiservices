<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JavaScript
 *
 * @author Usuario_2
 */
class JavaScript extends Controlador{
    
    public function doGet(){
        $nombre= $this->parametro_get("nombre");        
        if($nombre == NULL){
            $nombre= "base";
        }
        
        echo $this->twig->render("javascript/".$nombre.".html.twig");
    }
}

?>
