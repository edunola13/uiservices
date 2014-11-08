<?php
/**
 * Clase de la que deben extender los filtros de la aplicacion para funcionar correctamente
 *
 * @author Enola
 */
abstract class Filtro  {
    
    /**
     * Constructor que carga las librerias correspondientes
     */
    function __construct() {
        $this->cargar_librerias();
    }
    
    /**
     * Funcion que es llamada para realizar el filtro correspondiente
     */
    public abstract function filtrar();   
    
    /**
     * Carga uns instancia de la libreria en la instancia de la clase
     * @param Clase de la libreria $clase
     * @param string $nombre
     */
    protected function cargar_libreria($clase, $nombre = ""){
        agregar_instancia($clase, $this, $nombre);
    }
    
    /**
     * Carga una instancia de la clase en la instancia de la clase
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
        cargar_librerias_modulo($this, "filtro");
    }
    
}
?>