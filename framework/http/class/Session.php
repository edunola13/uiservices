<?php
namespace Enola\Http;
use Enola\Error;

session_start();
/**
 * Esta clase representa la session del usuario y va a proveer todo el comportamiento asociada a la session mediante
 * la funcionalidad que permite php. * 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class Session {    
    /**
     * Constructor que realiza la comprobacion de identidad
     */
    public function __constructor(){
        $this->checkIdentity();
    }    
    /**
     * Agrega un dato a la session mediante una clave
     * @param string $key
     * @param type $value
     */
    public function set($key,$value){
        $_SESSION[$key] = $value;
    }
    /**
     * Agrega un dato a la session mediante una clave serializandolo previamente
     * @param string $key
     * @param type $value
     */
    public function setSerialize($key,$value){
        $_SESSION[$key]= serialize($value);
    }
    /**
     * Devuelve un dato de la sesion o NULL si no existe
     * @param string $key
     * @return null o value
     */
    public function get($key){
        if (isset ($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        else {
            return NULL;
        }
    }
    /**
     * Devuelve un dato deserializado de la sesion o NULL si no existe
     * @param type $key
     * @return null o type
     */
    public function getUnserialize($key){
        if (isset ($_SESSION[$key])) {
            return unserialize($_SESSION[$key]);
        }
        else {
            return NULL;
        }
    }
    /**
     * Analza si existe una determinada clave asociada a la sesion
     * @param string $key
     * @return boolean
     */
    public function exist($key){
        if (isset ($_SESSION[$key])) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }    
    /**
     * Borra un dato asociado a la sesion
     * @param string $key
     */
    public function unsetVar($key){
        unset ($_SESSION[$key] ) ;
    }    
    /**
     * Borra la sesion
     */
    public function deleteSession(){
        $_SESSION = array() ;
        session_destroy();
    }    
    /**
     * Realiza una comprobacion de identidad
     * Analiza que no se este suplantando la identidad del verdadero usuario
     */
    private function checkIdentity(){
        if(isset($_SESSION['REMOTE_ADDR']) && isset($_SESSION['HTTP_USER_AGENT'])){
            if($_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] || $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
                Error::general_error('Session - Identity', 'There are a proble with the Sesion identity');
            }
        }
        else{
            $_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        }
    }    
}