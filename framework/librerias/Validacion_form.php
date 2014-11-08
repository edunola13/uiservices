<?php

/**
 * Libreria que realiza validacion de campos de formulario
 *
 * @author Enola
 */
class Validacion_form {
    //Variable con toda la informacion sobre los datos/campos
    private $campos_datos;

    private $archivo= NULL;
    private $messages;
    private $locale= NULL;
    
    public function __construct($locale = NULL) {
        $this->locale= $locale;
    }
    
    
    /**
     * Agregar una regla de validacion que luego sera validada
     * @param string $nombre
     * @param DATO $dato
     * @param array[string] $reglas
     */
    public function agregarRegla($nombre, $dato, $reglas){
        $this->campos_datos[$nombre] = array(
			'nombre'			=> $nombre,
			'dato'				=> $dato,
			'reglas'			=> explode('|', $reglas),
                        'valido'                        => TRUE,
                        'mensaje'                       => NULL
        );
    }
    
    /**
     * Ejecuta todas las reglas de validacion que se hayan cargado.
     * Devuelve TRUE si todas las reglas pasan y FALSE en caso contrario
     * @return boolean
     */
    public function validar(){
        if(count($this->campos_datos) == 0){
            //Si no hay datos devuelve TRUE
            return TRUE;
        }
        else{
            //Si hay datos valida sus reglas
            
            //El formulario empieza siendo valido
            $formulario_valido= TRUE;
            
            //Recorro todos los datos y sus reglas
            foreach ($this->campos_datos as $campos_dato) {
                $valido= TRUE;
                
                //Recorro cada regla del dato
                foreach ($campos_dato['reglas'] as $regla) {
                    //Ve el tipo de regla, con o sin parametros
                    if (count(explode('[', $regla)) > 1){
                        //Si hay reglas con parametros, separa la regla y su parametro               
                        $vars= explode('[', $regla);
                        $regla= $vars[0];
                        $var= explode(']', $vars[1]);
                        $var= $var[0];
                        //Realiza el llamado a la funcion correspondiente
                        $valido= call_user_func_array(array($this, $regla), array($campos_dato['nombre'], $campos_dato['dato'], $var));
                    }
                    else{
                        //Regla sin parametros
                        //Realiza el llamado a la funcion correspondiente
                        $valido= call_user_func_array(array($this, $regla), array($campos_dato['nombre'], $campos_dato['dato']));
                    }
                    
                    //Si una regla ya no es valida, no reviso las demas
                    if(!$valido){
                        //Asigno al campo Valido del dato FALSE
                        $nombre= $campos_dato['nombre'];
                        $this->campos_datos["$nombre"]['valido']= FALSE;
                        break;
                    }
                }
                
                //Actualizo el valor de formulario_valido de manera que si ya es falso se mantiene en falso
                $formulario_valido= $formulario_valido && $valido;
            }
            
            return $formulario_valido;
        }
    }
    
    /**
     * Devuelve los mensajes de error para cada campo que no haya pasado la validacion.
     * Es un array asociativo con el nombre del campo pasado
     * @return array[string]
     */
    public function mensajes_error(){
        $mensajes= NULL;
        foreach ($this->campos_datos as $campos_dato) {
            //Si no es valido agrego el mensaje de error
            if(! $campos_dato['valido']){
                $nombre= $campos_dato['nombre'];
                $mensajes["$nombre"]= $campos_dato['mensaje'];
            }
        }
        return $mensajes;
    }
    
    /**
     * Funcion utilizada internamente para agregar mensajes de error a los campos
     * @param string $nombre
     * @param string $mensaje
     */
    private function agregar_mensaje($nombre, $mensaje, $parametros = NULL){
        //Carga el archivo si es la primer llamada
        $this->cargar_messages();
        
        //Consigue el mensaje
        $mensaje= $this->messages[$mensaje];
        
        //Analiza si se pasaron parametros y si se pasaron cambia los valores correspondientes
        if($parametros != NULL){
            foreach ($parametros as $key => $valor) {
                $mensaje= str_replace(":$key", $valor, $mensaje);
            }
        }
        
        //Guarda el mensaje en el campo correspondiente
        $this->campos_datos["$nombre"]['mensaje']= $mensaje;
    }
    
    /**
     * Carga el archivo de mensajes en la primer llamada
     */
    private function cargar_messages(){
        if($this->archivo == NULL){
            if($this->locale == NULL){
                $this->messages= cargar_archivo_aplicacion("contenido/messages" . ".ini");
                $this->messages= parse_properties($this->messages);
            }
            else{
                $this->messages= cargar_archivo_aplicacion("contenido/messages" . "_$this->locale" . ".ini");
                $this->messages= parse_properties($this->messages);
            }
        }
    }
    
    /**
     * Regla requerido: analiza si el campo fue completado 
     * @param string $nombre
     * @param DATO $dato
     * @return boolean
     */
    private function requerido($nombre, $dato){
        if(! is_array($dato)){
            if($dato == ''){
                $this->agregar_mensaje($nombre, 'requerido');
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            if(count($dato) > 1){
                return TRUE;
            }
            else{
                return FALSE;
            }
        }
    }
    
    /**
     * Regla max_length: analiza que el string no contenga mas de $max caracteres
     * @param string $nombre
     * @param DATO $dato
     * @param int $max
     * @return boolean
     */
    private function max_length($nombre, $dato, $max){
        if(is_string($dato)){
            if(strlen($dato) > $max){
                $this->agregar_mensaje($nombre, "max_length", array('max' => $max));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->agregar_mensaje($nombre, 'es_string');
            return FALSE;
        }
    }
    
    /**
     * Regla min_lenght: analiza que el string no contenga menos de $min caracteres
     * @param string $nombre
     * @param DATO $dato
     * @param int $min
     * @return boolean
     */
    private function min_length($nombre, $dato, $min){
        if(is_string($dato)){
            if(strlen($dato) < $min){
                $this->agregar_mensaje($nombre, 'min_length', array('min' => $min));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->agregar_mensaje($nombre, 'es_string');
            return FALSE;
        }
    }
    
    /**
     * Regla es_integer: analiza que el campo sea un integer
     * @param string $nombre
     * @param DATO $dato
     * @return boolean
     */
    private function es_integer($nombre, $dato){
        if(is_numeric($dato)){
            return TRUE;
        }
        else{
            $this->agregar_mensaje($nombre, 'es_integer');
            return FALSE;
        }
    }
    
    /**
     * Regla max: analiza que el numero no se mayor a $max
     * @param string $nombre
     * @param DATO $dato
     * @param int $max
     * @return boolean
     */
    private function max($nombre, $dato, $max){
        if(is_numeric($dato)){
            $dato= (float)$dato;
            if($dato > $max){
                $this->agregar_mensaje($nombre, 'max', array('max' => $max));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->agregar_mensaje($nombre, 'es_integer');
            return FALSE;
        }
    }
    
    /**
     * Regla min: analiza que el numero no sea menor a $min
     * @param string $nombre
     * @param DATO $dato
     * @param int $min
     * @return boolean
     */
    private function min($nombre, $dato, $min){
        if(is_numeric($dato)){
            $dato= (float)$dato;
            if($dato < $min){
                $this->agregar_mensaje($nombre, 'min', array('min' => $min));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->agregar_mensaje($nombre, 'es_integer');
            return FALSE;
        }
    }

    /**
     * Regla igual: analiza si 2 datos son iguales
     * @param string $nombre
     * @param DATO $dato
     * @param DATO $acomparar
     * @return boolean
     */
    private function igual($nombre, $dato, $acomparar){
        if($dato == $this->campos_datos["$acomparar"]['dato']){
            return TRUE;
        }
        else{
            $this->agregar_mensaje($nombre, 'igual', array('acomparar' => $acomparar));
            return FALSE;
        }
    }
    
    /**
     * Regla email: analiza si el string cumple el formato de mail
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function email($nombre, $dato){
        if(filter_var($dato, FILTER_VALIDATE_EMAIL)){
            return TRUE; 
        }
        else{
            $this->agregar_mensaje($nombre, 'email');
            return FALSE;
        }
    }
    
    //Regla url: ve si el dato es un link correcto
    /**
     * Regla url: analiza si el string cumple el formato de URL
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function link($nombre, $dato){
        if(filter_var($dato, FILTER_VALIDATE_URL)){
            return TRUE; 
        }
        else{
            $this->agregar_mensaje($nombre, 'link');
            return FALSE;
        }
    }
    
    /**
     * Regla fecha: analiza si un string cumple el formato de fecha segun un formato pasado pasado como parametro
     * @param string $nombre
     * @param string $dato
     * @param string $formato (no obligatorio)
     * @return boolean
     */
    private function fecha($nombre, $dato, $formato){
        $tipo_separador= array(
            "/",
            "-",
            "."
        );
        
        $separador_usado= NULL;
        foreach ($tipo_separador as $separador) {
            $find= stripos($dato, $separador);
            if($find <> FALSE){
                $separador_usado= $separador;
            }
        }
        
        if($separador_usado != NULL){      
            $dato_array= explode($separador_usado, $dato);
            $valido= FALSE;
            switch ($formato){
                case 'Y-m-d':
                    if($separador_usado == '-'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;

                case 'd-m-Y':
                    if($separador_usado == '-'){
                        $valido= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;

                case 'd/m/Y':
                    if($separador_usado == '/'){
                        $valido= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;

                case 'Y/m/d':
                    if($separador_usado == '/'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;

                case 'Y.m.d':
                    if($separador_usado == '.'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;

                case 'd.m.Y':
                    if($separador_usado == '.'){
                        $valido= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;

                default :
                    if($separador_usado == '-'){
                        echo "Hola";
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;          
            }
        }
        else{
            $valido= false;
        }
        
        if($valido){
            return TRUE;
        }
        else{
            $this->agregar_mensaje($nombre, 'fecha', array('formato' => $formato));
            return FALSE;
        }
        
    }
    
}
?>