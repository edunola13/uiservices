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
class JavaScript extends En_Controller{
    public function __construct() {
        parent::__construct();
    }    
    
    public function doGet(){
        $nombre= $this->request->param_get("nombre");        
        if($nombre == NULL){
            $nombre= "base";
        }
        
        echo $this->twig->render("javascript/".$nombre.".html.twig");
    }
}

?>
