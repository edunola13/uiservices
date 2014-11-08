<?php
/**
 * Clase de la que deben extender los controladores de la aplicacion para que se asegure el funcioneamiento del mismo
 * 
 * @author Enola
 */
class Controlador {
    //parametros_uri es cargado por el modulo ruteo
    public $parametros_uri;
    public $parametros_get;
    public $parametros_post;
    
    //errores
    public $errores;
    
    /**
     * Constructor que carga las librerias y carga los parametros limpiandolos
     */
    function __construct(){
        $this->cargar_librerias();
        $this->parametros_get= limpiar_variables($_GET);
        $this->parametros_post= limpiar_variables($_POST);
    }
    
    /**
     * Funcion que es llamada cuando el metodo HTTP es GET
     */
    public function doGet(){
        
    }
    
    /**
     * Funcion que es llamada cuando el metodo HTTP es POST
     */
    public function doPost(){
        
    }
    
    /**
     * Funcion que es llamada cuando el metodo HTTP es DELETE
     */
    public function doDelete(){
        
    }
    
    /**
     * Funcion que es llamada cuando el metodo HTTP es PUT
     */
    public function doPut(){
        
    }
    
    public function doOptions(){
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    }
    
    /**
     * Funcion lee los campos de un formulario
     */
    protected function leer_campos(){
        
    }
    
    /**
     * Funcion que valido los campos de un formulario
     */
    protected function validar(){
        
    }
    
    /**
     * Funcion que actua cuando acurre un error en la validacion
     */
    protected function error(){
        
    }
    
    /**
     * Funcion que carga los datos usados por la vista
     */
    protected function cargar_datos(){
        
    }
    
    /**
     * Funcion que carga los datos usados por la vista de 
     */
    protected function cargar_datos_error(){
        
    }
    
    /**
     * Devuelve un parametro GET si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    protected function parametro_get($nombre){
        if(isset($this->parametros_get[$nombre])){
            return $this->parametros_get[$nombre];
        }
        else{
            return NULL;
        }
    }
    
    /**
     * Devuelve un parametro POST si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    protected function parametro_post($nombre){
        if(isset($this->parametros_post[$nombre])){
            return $this->parametros_post[$nombre];
        }
        else{
            return NULL;
        }
    }
    
    /**
     * Devuelve un parametro "URI" si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    protected function parametro_uri($nombre){
        if(isset($this->parametros_uri[$nombre])){
            return $this->parametros_uri[$nombre];
        }
        else{
            return NULL;
        }
    }
    
    
    /**
     * Agrega la instancia de una libreria a la instancia de una clase que extienda de Controlador
     * @param Clase de la Libreria $clase
     * @param string $nombre
     */
    protected function cargar_libreria($clase, $nombre = ""){
        agregar_instancia($clase, $this, $nombre);
    }
    
    /**
     * Agrega la instancia de una Clase a la instancia de una clase que extienda de Controlador
     * @param Clase $clase
     * @param string $nombre
     */
    protected function cargar_clase($clase, $nombre= ""){
        agregar_instancia($clase, $this, $nombre);
    }
    
    /**
     * Metodo llamado en el constructor de la clase que carga las librerias correspondientes
     */
    private function cargar_librerias(){
        //Realiza el llamado a la funcion que se encarga de esto
        cargar_librerias_modulo($this, "controlador");
    }
    
}
?>