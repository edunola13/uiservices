<?php
session_start();

/**
 * Libreria que maneja los datos y la seguridad de la sesion
 *
 * @author Enola
 */
class Sesion {
    
    /**
     * Constructor que realiza la comprobacion de identidad
     */
    public function __constructor(){
        $this->comprobar_identidad();
    }
    
    /**
     * Setea un dato a la sesion
     * @param string $nombre
     * @param DATO $valor
     */
    public function set($nombre,$valor){
        $_SESSION[$nombre] = $valor;
    }
    
    /**
     * Devuelve un dato de la sesion o NULL si no existe
     * @param string $nombre
     * @return NULL o DATO
     */
    public function get($nombre){
        if (isset ($_SESSION[$nombre])) {
            return $_SESSION[$nombre];
        }
        else {
            return NULL;
        }
    }
    
    /**
     * Analza si existe un determinado dato asociado a la sesion
     * @param string $nombre
     * @return boolean
     */
    public function existe($nombre){
        if (isset ($_SESSION[$nombre])) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    /**
     * Borra un dato asociado a la sesion
     * @param string $nombre
     */
    public function borrar_variable($nombre){
        unset ($_SESSION[$nombre] ) ;
    }
    
    /**
     * Borra la sesion
     */
    public function borrar_sesion(){
        $_SESSION = array() ;
        session_destroy();
    }
    
    /**
     * Realiza una comprobacion de identidad
     * Analiza que se este suplantando la identidad del verdadero usuario
     */
    private function comprobar_identidad(){
        if(isset($_SESSION['REMOTE_ADDR']) && isset($_SESSION['HTTP_USER_AGENT'])){
            if($_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] || $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
                mostrar_error("Sesion - Identidad", "Hay un problema de identidad de la sesion");
            }
        }
        else{
            $_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        }
    }
    
}
?>