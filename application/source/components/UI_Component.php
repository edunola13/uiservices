<?php
use Enola\Component;
use Enola\Support\Request, Enola\Support\Response;

class UI_Component extends Component\En_Component{
    private $config;
    private $twig;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Renderiza el componente
     * Devuelve el componente listo para mostrar como HTML o devuelve la definicion del mismo para las APIs
     * Dependiendo los parametros pasados
     * @param type $parametros
     * @return type 
     */
    public function rendering(Request $request, Response $response, $params = NULL) {
        if(isset($params["component"])){
            $this->injectDependency($this->context->app, 'twig', 'twig');
            //Imprime el componente con la configuracion y componentes(hijos) correspondientes
            $componente= $params["component"];
            $config= NULL;
            if(isset($componente["configuracion"])){
                $config= $componente["configuracion"];
            }
            $childComponents= $params["sons"];            
            echo $this->printComponent($componente["nombre"], $childComponents, $config);            
        }
        else{
            //Devuelve la definicion del componente para las APIs de los lenguajes
            echo $this->definition($params['name']);
        }
    }
    
    public function setConfig($config){
        $this->config= $config;
    }
    public function setTwig($twig){
        $this->twig= $twig;
    }

    /**
     * Ejecuta la plantilla correspondiente del componente de TWIG
     * @param type $name
     * @param type $sons
     * @param type $config
     * @return string 
     */
    protected function printComponent($name, $sons, $config){
        $project= $this->config->actualProjectConfig();
        $folderView= $project['components'][$name] . '/';
        //Le quito el "/" en caso de que no haya carpeta
        $folderView= ltrim($folderView, '/');
        $base= $project['base'];
        return $this->twig->render("components/" . $base . '/' . $folderView . $name . ".html.twig", array("childComponents" => $sons, "config" => $config));
    }    
    /**
     * Arma la definicion del componente
     * @param type $name
     * @return string 
     */
    protected function definition($name){
        //Respuesta a devolver
        $html= "";
        //Cargo el archivo en un string
        $project= $this->config->actualProjectConfig();
        $folderView= $project['components'][$name];
        $base= $project['base'];
        $file= file_get_contents(PATHAPP . "source/view/components/" . $base . '/' . $folderView . '/' . $name . ".html.twig");
        //Analizo la herencia y armo los bloques en base a esta
        $bloques= $this->inheritance($file);
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
                    $i= $this->jumpSpaces($cadena, $i);
                    //Si encuentro la variable "hijos" la reemplazo por "components"
                    if($cadena[$i] == "c"){
                        $i= $this->findWord($cadena, "childComponents", $i);
                        if($i !== FALSE && ($cadena[$i] == " " || $cadena[$i] == "|")){
                            $i++;
                            $i= $this->findAndJump($cadena, $i, "}");
                            $i= $this->find($cadena, $i, "}");
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
        //Quito saltos de Lineas y demas
        $html = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $html);
        return $html;
    }    
    /**
     * Armo la herencia del componente
     * Metodo recursivo que ira llamando a los respectivos padres para ir armando los blooques y luego sobreescribirlos
     * si corresponde. 
     * @param type $file
     * @return blocks
     */
    private function inheritance($file){
        $i= 0;
        $bloquesPadre= NULL;
        //Analizar herencia        
        $i= $this->jumpSpaces($file, $i);
        if($file[$i] == "{" && $file[$i + 1] == "%"){
            $i+= 2;
            $i= $this->jumpSpaces($file, $i);
            $i= $this->findWord($file, "extends", $i);
            if($i !== FALSE && $file[$i] == " "){
                $ini= $this->findAndJump($file, $i, '"');
                $fin= $this->find($file, $ini, '"');
                $archivo= substr($file, $ini, $fin - $ini);
                $padreFile= file_get_contents(PATHAPP . "source/view/" . $archivo); 
                $bloquesPadre= $this->inheritance($padreFile);
            }
        }
        if($bloquesPadre == NULL){
            $bloques= $this->buildBlocks($file);
        }
        else{
            $bloques= $this->buildBlocks($file, $bloquesPadre);
        }
        return $bloques;
    }    
    /**
     * Arma los bloques en base a toda la herencia
     * @param type $file
     * @param type $blocks
     * @return type 
     */
    private function buildBlocks($file, $blocks = array()){
        $i= 0;
        $inicioBloque= 0;
        $finBloque= 0;
        $nombreBloque= "";
        while($i < strlen($file)){
            if($file[$i] == "{" && $file[$i + 1] == "%"){
                $posInicio= $i;
                $i+= 2;
                $i= $this->jumpSpaces($file, $i);
                if($file[$i] == "b"){
                    $i= $this->findWord($file, "block", $i);
                    if($i !== FALSE){
                        $nombreBloque= $this->blockName($file, $i);
                        $i= $this->findAndJump($file, $i, "}");
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
                        $i= $this->findAndJump($file, $i, "}");
                        $blocks[$nombreBloque]= substr($file, $inicioBloque, $finBloque - $inicioBloque + 1);
                    }
                }
            }
            else{
                $i++;
            }
        }
        return $blocks;
    }    
    /**
     * Busca una palabra en un string desde una posicion
     * Retorna el numero de la posicion siguiente a la palbra si existe la misma o FALSE si no existe
     * @param type $file
     * @param type $toFind
     * @param type $from
     * @return boolean 
     */
    private function findWord($file, $toFind, $from){
        $i= $from;
        $palabra= $file[$i];
        $i++;
        while($i < strlen($file) && strpos($toFind, $palabra . $file[$i]) !== FALSE){
            $palabra .= $file[$i];
            $i++;
        }
        if(strcmp($palabra, $toFind) == 0){
            return $i;
        }
        else{
            return FALSE;
        }
    }
    /**
     * Salta todos los blancos consecutivos que aparezcan en un string desde una posicion
     * Retorna la posicion siguiente al ultimo blanco o FALSE si son todos blancos o se termina el string
     * @param type $file
     * @param type $from
     * @return boolean 
     */
    private function jumpSpaces($file, $from){
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
    /**
     * Salta todos los blancos consecutivos que aparezcan en un string desde el final
     * Retorna la posicion siguiente al ultimo blanco o FALSE si son todos blancos o se termina el string
     * @param type $file
     * @return boolean
     */
    private function jumpSpacesFromBehind($file){
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
     * @param type $char
     * @return boolean 
     */
    private function findAndJump($file, $from, $char){
        $i= $from;
        while($i < strlen($file) && $file[$i] != $char){
            $i++;
        }
        if($file[$i] == $char){
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
     * @param type $char
     * @return boolean 
     */
    private function find($file, $from, $char){
        $i= $from;
        while($i < strlen($file) && $file[$i] != $char){
            $i++;
        }
        if($file[$i] == $char){
            return $i;
        }
        else{
            return FALSE;
        }
    }    
    /**
     * Devuleve el nombre del bloque pasado
     * @param type $file
     * @param type $from
     * @return type 
     */
    private function blockName($file, $from){
        $i= $from;
        $i= $this->jumpSpaces($file, $from);
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
//                $i= $this->jumpSpaces($file, $i);
//                
//                //Analizamos que caracter se encontro para buscar coincidencia
//                switch ($file[$i]){
//                    case "b":
//                        $i= $this->buscarPalabra($file, "block", $i);
//                        if($i !== FALSE && $file[$i] == " "){
//                            $i++;
//                            $i= $this->findAndJump($file, $i, "}");
//                            $agregar= FALSE;
//                        }                        
//                        if($agregar){
//                            $i= $posInicio;
//                        }
//                        break;
//                    case "e":
//                        //Agregar extends
//                        $i= $this->findWord($file, "endblock", $i);
//                        if($i !== FALSE && ($file[$i] == " " || $file[$i] == "%")){
//                            $i++;
//                            $i= $this->findAndJump($file, $i, "}");
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
//                    $i= $this->jumpSpaces($file, $i);
//                    if($file[$i] == "h"){
//                        $i= $this->findWord($file, "childComponents", $i);
//                        if($i !== FALSE && ($file[$i] == " " || $file[$i] == "|")){
//                            $i++;
//                            $i= $this->findAndJump($file, $i, "}");
//                            $i= $this->findAndJump($file, $i, "}");
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