<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

class Component extends Http\En_Controller{
    public $config;
    
    public function __construct() {        
        parent::__construct();
    }
    
    public function doPost(En_HttpRequest $request, En_HttpResponse $response){
        //Accedo al cuerpo de la peticion para poder leer el JSON
        $jsonBody = @file_get_contents('php://input');        
        //Consigo el archivo JSON de la peticion y lo decodifico a array
        $component= json_decode($jsonBody, TRUE);
        
        if($this->getUriParam('project') != NULL){
            $this->config->loadProject($this->getUriParam('project'));
            /**
             * Ahora llamo a la funcion que en base al archivo JSON arma la RTA de los componentes. Esta ira ejecutando
             * todos los componentes antes ejecutando los sub componentes de los componentes y asi recursivamente.
             * Devuelve el codigo de todos los componentes
             */
            $sons= NULL;
            if(isset($component["childComponents"])){
                $sons= $this->buildComponents($component["childComponents"]);
            }
            $components= "";
            if(isset($component["nombre"])){
                $components= $this->component('ui-component', array("component" => $component, "sons" => $sons), NULL, TRUE);
            }

            //Modifico el Header
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

            echo $components;
        }
    }
    
    /**
     * Esta funcion recibe un conjunto de componentes y los va ejecutando, si los componentes pasados tienen sub
     * componentes esta funcion se llamara recursivamente hasta que no haya mas sub componentes.
     * @param type $components
     * @return type 
     */
    private function buildComponents($components){
        $rtaComponents= NULL;
        //Recorro todos los componentes
        foreach ($components as $component) {
            $childComponents= NULL;
            //Para cada componente analizo si tiene mas componentes
            if(isset($component["childComponents"])){
                //Si tiene mas componentes los llamo para que me devuelva la rta
                $childComponents= $this->buildComponents($component["childComponents"]);
            }
            //Ejecuto el componente actual para que devuelva su rta con la rta de sus componentes
            
            $rtaComponents .= $this->component('ui-component', array("component" => $component, "sons" => $childComponents), NULL, TRUE);
        }       
        //Retorno el codigo de los componentes
        return $rtaComponents;
    }
}