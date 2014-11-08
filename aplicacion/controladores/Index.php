<?php

/**
 * Description of Index
 *
 * @author Enola
 */

class Index extends Controlador{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function doGet(){
         
    }
    
    public function doPost(){
        //Accedo al cuerpo de la peticion para poder leer el JSON
        $jsonBody = @file_get_contents('php://input');
        
        //Consigo el archivo JSON de la peticion y lo decodifico a array
        $config= json_decode($jsonBody, TRUE);

        /**
         * Ahora llamo a la funcion que en base al archivo JSON arma la RTA de los componentes. Esta ira ejecutando
         * todos los componentes antes ejecutando los sub componentes de los componentes y asi recursivamente.
         * Devuelve el codigo de todos los componentes
         */
        $componentes= $this->armarComponentes($config["componentes"]);
             
        /**
         *Analizo si se usa o no layout, si se usa layout tengo que ver si se pasa el nombre o se usa la por defecto 
         */
        if($config["layout"]["habilitado"] == "TRUE"){
            //Imprimo los componente dentro de un layout que va a tener CSS, JS, ETC.
            if(isset($config["layout"]["nombre"])){
                echo $this->twig->render("layouts/" . $config["layout"]["nombre"] . ".html.twig", array("componentes" => $componentes));
            }
            else{
                echo $this->twig->render("base.html.twig", array("componentes" => $componentes));
            }
        }
        else{
            //Imprimo solo la estructura HTML de los componentesz
            echo $componentes;
        }   
    }
    
    
    /**
     * Esta funcion recibe un conjunto de componentes y los va ejecutando, si los componentes pasados tienen sub
     * componentes esta funcion se llamara recursivamente hasta que no haya mas sub componentes.
     * @param type $componentes
     * @return rtaComponentes 
     */
    private function armarComponentes($componentes){
        $rtaComponentes= NULL;
        //Recorro todos los componentes
        foreach ($componentes as $componente) {
            $componentesHijos= NULL;
            //Para cada componente analizo si tiene mas componentes
            if(isset($componente["componentes"])){
                //Si tiene mas componentes los llamopara que me devuelva la rta
                $componentesHijos= $this->armarComponentes($componente["componentes"]);
            }
            //Ejecuto el componente actual para que devuelva su rta con la rta de sus componentes
            $rtaComponentes .= componente($componente["nombre"], array("componente" => $componente, "hijos" => $componentesHijos));
        }       
        //Retorno el codigo de los componentes
        return $rtaComponentes;
    }
}

?>
