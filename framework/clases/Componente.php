<?php
/**
 * Clase de la que deben extender los componentes de la aplicacion para que se asegure el funcioneamiento del mismo
 * 
 * @author Enola
 */

/**
 * Description of Componente
 *
 * @author Enola
 */
class Componente {
    
    public function __construct() {
        $this->cargar_librerias();
    }
    
    /**
     * Funcion que es llamada para que el componente realice su trabajo
     */
    public function renderizar($parametros = NULL){
        
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
        cargar_librerias_modulo($this, "componente");
    }
}

?>
