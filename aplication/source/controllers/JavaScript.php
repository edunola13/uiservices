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
    private $twig;
    
    public function __construct() {
        parent::__construct();
        $this->twig= new Twig();
    }    
    
    public function doGet(){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        $nombre= $this->request->param_get("nombre");        
        if($nombre == NULL){
            $nombre= "base";
        }        
        echo $this->twig->render("javascript/".$nombre.".html.twig");
    }
}

?>
