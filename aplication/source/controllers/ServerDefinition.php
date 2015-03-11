<?php
class ServerDefinition extends En_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Devuelve la definicion del servidor. Digamos todos sus componentes
     */
    public function doGet(){
        //Recorro todos los proyectos
            //Con el archivo de configuracion recorro todos los componentes del proyecto
                //Voy guardando con clave-valor lo que retorna la definicion del componente
            //Con el archivo de configuracion recorro todos los javascript del proyecto
                //Voy guardando lo que devuelva el twig del javascript
            //Con el archivo de configuracion recorro todos los themes del proyecto
                //Voy guardando lo que devuelva el twig del theme
        
        //clave de cada componente-javascript-theme: proyecto_(component, javascript o theme)_nombre
        
        //Obligar descarga y segun el tipo agregar extension .txt o .properties
    }
}

?>
