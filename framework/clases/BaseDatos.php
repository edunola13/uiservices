<?php
/**
 * Clase que se encarga de la configuracion de la BD.
 * Para utilizar la configuracion del Framework es necesario que las clases extiendan de esta clase
 *
 * @author Enola
 */
class BaseDatos {
    protected $bd;
    
    /**
     * Constructor que conecta a la bd y carga las librerias que se indicaron en el archivo de configuracion
     */
    function __construct() {
        $this->bd= conectar_bd();
        $this->cargar_librerias();
    }
    
    
    /**
     * Patron Active Record
     */
    
    
    
    /**
     * Funciones para manejar la clase
     */
    
    /**
     * Carga una libreria
     * @param string $clase
     * @param string $nombre
     */
    protected function cargar_libreria($clase, $nombre = ""){
        agregar_instancia($clase, $this, $nombre);
    }
    
    
    /**
     * Carga la instancia de una clase
     * @param string $clase
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
        cargar_librerias_modulo($this, "bd");
    }
}
?>