<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Theme
 *
 * @author Usuario_2
 */
class Theme extends Controlador{
    
    public function doGet(){
        $nombre= $this->parametro_get("nombre");
        if($nombre == NULL){
            $nombre= "base";
        }
        
        echo $this->twig->render("theme/".$nombre.".html.twig");
    }
}

?>
