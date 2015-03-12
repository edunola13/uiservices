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
        
        //Cargo la configuracion y la configuracion para el proyecto default o sleccionado
        $config= Config::getInstance();
        $proyecto= $config->defaultProject();
        if($this->request->param_get("proyecto") != NULL){
            $proyecto= $this->request->param_get("proyecto");
        }
        $config->loadProject($proyecto);
        
        //Agarro el nombre desde un parametro GET
        $nombre= $this->request->param_get("nombre");
        if($nombre != NULL){
            echo component('ui_component', array("nombre" => $nombre));
        }
    }

}

?>
