<?php
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
        
        /**
         * Seteo el proyecto 
         */
        $proyecto= 'bootstrap3';
        if($this->request->param_get("proyecto") != NULL){
            $proyecto= $this->request->param_get("proyecto");
        }
        define('PROYECTO_UI', $proyecto);
        
        //Agarro el nombre desde un parametro GET
        $nombre= $this->request->param_get("nombre");
        if($nombre != NULL){
            echo component('ui_component', array("nombre" => $nombre));
        }
    }

}

?>
