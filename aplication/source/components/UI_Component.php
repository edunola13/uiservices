<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtensionComponente
 *
 * @author Usuario_2
 */
class UI_Component extends En_Component{
    protected $config;
    
    public function __construct() {
        parent::__construct();
        $this->config= file_get_contents(PATHAPP . CONFIGURATION . "components.json");
        $this->config= json_decode($this->config, TRUE);
    }

    /**
     * Renderiza el componente
     * Devuelve el componente listo para mostrar como HTML o devuelve la definicion del mismo para las APIs
     * Dependiendo los parametros pasados
     * @param type $parametros
     * @return type 
     */
    public function rendering($params = NULL) {      
        if(isset($params["componente"])){
            //Imprime el componente con la configuracion y componentes(hijos) correspondientes
            $componente= $params["componente"];
            $config= NULL;
            if(isset($componente["configuracion"])){
                $config= $componente["configuracion"];
            }
            $datos=  NULL;
            if(isset($componente["datos"])){
                $datos= $componente["datos"];
            }
            $componentesHijos= $params["hijos"];
            return $this->imprimir($componente["nombre"], $componentesHijos, $config, $datos);
        }
        else{
            //Devuelve la definicion del componente para las APIs de los lenguajes
            return $this->definicion($params["nombre"]);
        }
    }
    
    /**
     * Levanta la plantilla correspondiente de TWIG
     * @param type $nombre
     * @param type $hijos
     * @param type $config
     * @param type $datos
     * @return type 
     */
    protected function imprimir($nombre, $hijos, $config, $datos){
        $folderView= $this->config[$nombre];
        return $this->twig->render("componentes/" . $folderView . '/' . $nombre . ".html.twig", array("hijos" => $hijos, "config" => $config, "datos" => $datos));
    }
    
    /**
     * Arma la definicion del componente
     * @param type $nombre
     * @return string 
     */
    protected function definicion($nombre){
        //Respuesta a devolver
        $html= "";      

        //Cargo el archivo en un string
        $folderView= $this->config[$nombre];
        $file= file_get_contents(PATHAPP . "source/view/componentes/" . $folderView . '/' . $nombre . ".html.twig");
        //Analizo la herencia y armo los bloques en base a esta
        $bloques= $this->herencia($file);
        
        //Recorro todos los bloques para armar la respuesta correcta
        foreach ($bloques as $cadena) {
            $i= 0;
            while($i < strlen($cadena)){
                $agregar= TRUE;
                $modificar= FALSE;
                $modificacion= "";
                if($cadena[$i] == "{" && $cadena[$i + 1] == "{"){
                    $posInicio= $i;
                    //Es mas 2 por tengo que saltar "{"
                    $i+= 2;
                    //Busca el primer caracter que no sea vacio
                    $i= $this->saltarBlancos($cadena, $i);
                    //Si encuentro la variable "hijos" la reemplazo por "components"
                    if($cadena[$i] == "h"){
                        $i= $this->buscarPalabra($cadena, "hijos", $i);
                        if($i !== FALSE && ($cadena[$i] == " " || $cadena[$i] == "|")){
                            $i++;
                            $i= $this->buscarYsaltar($cadena, $i, "}");
                            $i= $this->buscar($cadena, $i, "}");
                            $agregar= FALSE;
                            $modificar= TRUE;
                        }
                    }
                    if($agregar){
                        $i= $posInicio;
                    }
                    if($modificar){
                        $modificacion= "{{components}}";
                    }
                }
                //Analizo si agrego el caracter como viene o si hay que modificar
                if($agregar){
                    $html .= $cadena[$i];
                }
                if($modificar){
                    $html .= $modificacion;
                }
                $i++;
            }
        }
        return $html;
    }
    
    /**
     * Armo la herencia del componente
     * Metodo recursivo que ira llamando a los respectivos padres para ir armando los blooques y luego sobreescribirlos
     * si corresponde. 
     * @param type $file
     * @return bloques
     */
    private function herencia($file){
        $i= 0;
        $bloquesPadre= NULL;
        //Analizar herencia        
        $i= $this->saltarBlancos($file, $i);
        if($file[$i] == "{" && $file[$i + 1] == "%"){
            $i+= 2;
            $i= $this->saltarBlancos($file, $i);
            $i= $this->buscarPalabra($file, "extends", $i);
            if($i !== FALSE && $file[$i] == " "){
                $ini= $this->buscarYsaltar($file, $i, '"');
                $fin= $this->buscar($file, $ini, '"');
                $archivo= substr($file, $ini, $fin - $ini);
                $padreFile= file_get_contents(PATHAPP . "source/view/" . $archivo); 
                $bloquesPadre= $this->herencia($padreFile);
            }
        }
        if($bloquesPadre == NULL){
            $bloques= $this->armarBloques($file);
        }
        else{
            $bloques= $this->armarBloques($file, $bloquesPadre);
        }
        return $bloques;
    }
    
    /**
     * Arma los bloques
     * @param type $file
     * @param type $bloques
     * @return type 
     */
    private function armarBloques($file, $bloques = array()){
        $i= 0;
        $inicioBloque= 0;
        $finBloque= 0;
        $nombreBloque= "";
        while($i < strlen($file)){
            if($file[$i] == "{" && $file[$i + 1] == "%"){
                $posInicio= $i;
                $i+= 2;
                $i= $this->saltarBlancos($file, $i);
                if($file[$i] == "b"){
                    $i= $this->buscarPalabra($file, "block", $i);
                    if($i !== FALSE){
                        $nombreBloque= $this->nombreBloque($file, $i);
                        $i= $this->buscarYsaltar($file, $i, "}");
                        $inicioBloque= $i;
                    }
                }
                if($file[$i] == "e"){
                    $finBloque= $posInicio - 1;
                    //ACA DEBERIA USAR LA FUNCION buscarPalabra PERO POR UNA PURA RAZON QUE NO ENTIENDO SE QUEDA ITERANDO
                    $palabra= $file[$i];
                    $i++;
                    while($i < strlen($file) && strpos("endblock", $palabra . $file[$i]) !== FALSE){
                        $palabra .= $file[$i];
                        $i++;
                    }
                    if($palabra == "endblock"){
                        $i= $this->buscarYsaltar($file, $i, "}");
                        $bloques[$nombreBloque]= substr($file, $inicioBloque, $finBloque - $inicioBloque + 1);
                    }
                }
            }
            else{
                $i++;
            }
        }
        return $bloques;
    }
    
    
    /**
     * Busca una palabra en un string desde una posicion
     * Retorna el numero de la posicion siguiente a la palbra si existe la misma o FALSE si no existe
     * @param type $file
     * @param type $aBuscar
     * @param type $from
     * @return boolean 
     */
    private function buscarPalabra($file, $aBuscar, $from){
        $i= $from;
        $palabra= $file[$i];
        $i++;
        while($i < strlen($file) && strpos($aBuscar, $palabra . $file[$i]) !== FALSE){
            $palabra .= $file[$i];
            $i++;
        }
        if(strcmp($palabra, $aBuscar) == 0){
            return $i;
        }
        else{
            return FALSE;
        }
    }

    /**
     * Salto todos los blancos consecutivos que aparezcan en un string desde una posicion
     * Retorna la posicion siguiente al ultimo blanco o FALSE si son todos blancos o se termina el string
     * @param type $file
     * @param type $from
     * @return boolean 
     */
    private function saltarBlancos($file, $from){
        $i= $from;
        while($i < strlen($file) && $file[$i] == " "){
            $i++;
        }
        if($file[$i] != " "){
            return $i;
        }
        else{
            return FALSE;
        }
    }
    
    private function saltarBlancosDesdeAtras($file){
        $i= strlen($file) - 1;
        while($i > 0 && $file[$i] == " "){
            $i--;
        }
        if($file[$i] != " "){
            return $i;
        }
        else{
            return FALSE;
        }
    }
    
    /**
     * Busca un caracter y lo salta
     * @param type $file
     * @param type $from
     * @param type $caracter
     * @return boolean 
     */
    private function buscarYsaltar($file, $from, $caracter){
        $i= $from;
        while($i < strlen($file) && $file[$i] != $caracter){
            $i++;
        }
        if($file[$i] == $caracter){
            $i++;
            return $i;
        }
        else{
            return FALSE;
        }
    }
    
    /**
     * Encuentra un caracter y devuelve su ubicacion
     * @param type $file
     * @param type $from
     * @param type $caracter
     * @return boolean 
     */
    private function buscar($file, $from, $caracter){
        $i= $from;
        while($i < strlen($file) && $file[$i] != $caracter){
            $i++;
        }
        if($file[$i] == $caracter){
            return $i;
        }
        else{
            return FALSE;
        }
    }
    
    /**
     * Devuleve le nombre del bloque pasado
     * @param type $file
     * @param type $from
     * @return type 
     */
    private function nombreBloque($file, $from){
        $i= $from;
        $i= $this->saltarBlancos($file, $from);
        $ini= $i;
        while($i < strlen($file) && ($file[$i] != " " && $file[$i] != "%")){
            $i++;
        }
        return substr($file, $ini, $i - $ini);
    }
}


/**
 * Codigo viejo de armado de codigo 
 */
//        while($i < strlen($file)){
//            $agregar= TRUE;
//            $modificar= FALSE;
//            $modificacion= "";
//            
//            //Analiza si puede ser un BLOCK
//            if($file[$i] == "{" && $file[$i + 1] == "%"){
//                $posInicio= $i;
//                //Es mas 2 por tengo que saltar "%"
//                $i+= 2;
//                $i= $this->saltarBlancos($file, $i);
//                
//                //Analizamos que caracter se encontro para buscar coincidencia
//                switch ($file[$i]){
//                    case "b":
//                        $i= $this->buscarPalabra($file, "block", $i);
//                        if($i !== FALSE && $file[$i] == " "){
//                            $i++;
//                            $i= $this->buscarYsaltar($file, $i, "}");
//                            $agregar= FALSE;
//                        }                        
//                        if($agregar){
//                            $i= $posInicio;
//                        }
//                        break;
//                    case "e":
//                        //Agregar extends
//                        $i= $this->buscarPalabra($file, "endblock", $i);
//                        if($i !== FALSE && ($file[$i] == " " || $file[$i] == "%")){
//                            $i++;
//                            $i= $this->buscarYsaltar($file, $i, "}");
//                            $agregar= FALSE;
//                        }                        
//                        if($agregar){
//                            $i= $posInicio;
//                        }
//                        break;
//                    case "i":
//                        $i= $posInicio;
//                        break;
//                    case "f":
//                        $i= $posInicio;
//                        break;
//                }
//            }
//            else{
//                if($file[$i] == "{" && $file[$i + 1] == "{"){
//                    $posInicio= $i;
//                    //Es mas 2 por tengo que saltar "{"
//                    $i+= 2;
//                    //Busca el primer caracter que no sea vacio
//                    $i= $this->saltarBlancos($file, $i);
//                    if($file[$i] == "h"){
//                        $i= $this->buscarPalabra($file, "hijos", $i);
//                        if($i !== FALSE && ($file[$i] == " " || $file[$i] == "|")){
//                            $i++;
//                            $i= $this->buscarYsaltar($file, $i, "}");
//                            $i= $this->buscarYsaltar($file, $i, "}");
//                            $agregar= FALSE;
//                            $modificar= TRUE;
//                        }
//                    }
//                    if($agregar){
//                        $i= $posInicio;
//                    }
//                    if($modificar){
//                        $modificacion= "{{components}}";
//                    }
//                }
//            }
//            
//            if($agregar){
//                $html .= $file[$i];
//            }
//            if($modificar){
//                $html .= $modificacion;
//            }
//            $i++;
//        }
//        //Respuesta
//        return $html;

?>