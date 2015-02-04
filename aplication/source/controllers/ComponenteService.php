<?php
/**
 * Description of Componente
 *
 * @author Usuario_2
 */
class ComponenteService extends En_Controller{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Imprime un componente
     * Es por POST ya que se pasan datos JSON 
     */
    public function doPost(){        
        //Accedo al cuerpo de la peticion para poder leer el JSON
        $jsonBody = @file_get_contents('php://input');        
        //Consigo el archivo JSON de la peticion y lo decodifico a array
        $componente= json_decode($jsonBody, TRUE);
        /**
         * Seteo el proyecto 
         */
        $proyecto= 'bootstrap3';
        if(isset($componente["proyecto"])){
            $proyecto= $componente["proyecto"];
        }
        define('PROYECTO_UI', $proyecto);
        /**
         * Ahora llamo a la funcion que en base al archivo JSON arma la RTA de los componentes. Esta ira ejecutando
         * todos los componentes antes ejecutando los sub componentes de los componentes y asi recursivamente.
         * Devuelve el codigo de todos los componentes
         */
        $hijos= NULL;
        if(isset($componente["componentes"])){
            $hijos= $this->armarComponentes($componente["componentes"]);
        }
        if(isset($componente["nombre"])){
            $componentes= component('ui_component', array("componente" => $componente, "hijos" => $hijos));
        }
                
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        echo $componentes;
    }
    
    /**
     * Esta funcion recibe un conjunto de componentes y los va ejecutando, si los componentes pasados tienen sub
     * componentes esta funcion se llamara recursivamente hasta que no haya mas sub componentes.
     * @param type $componentes
     * @return type 
     */
    private function armarComponentes($componentes){
        $rtaComponentes= NULL;
        //Recorro todos los componentes
        foreach ($componentes as $componente) {
            $componentesHijos= NULL;
            //Para cada componente analizo si tiene mas componentes
            if(isset($componente["componentes"])){
                //Si tiene mas componentes los llamo para que me devuelva la rta
                $componentesHijos= $this->armarComponentes($componente["componentes"]);
            }
            //Ejecuto el componente actual para que devuelva su rta con la rta de sus componentes
            $rtaComponentes .= component('ui_component', array("componente" => $componente, "hijos" => $componentesHijos));
        }       
        //Retorno el codigo de los componentes
        return $rtaComponentes;
    }
}

?>