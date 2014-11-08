<?php
    /*
     * Conjunto de funciones para cargar archivos del framewrok y de la aplicacion
     */

    /**
     * Carga una libreria del framework pasando una direccion
     * @param string $dir
     */
    function importar_libreria_framework($dir){
        $dir= PATHFRA . "librerias/" . $dir . ".php";
        require_once $dir;
    }
    

    /**
     * Carga una libreria de la aplicacion pasando una direccion
     * @param string $dir
     */
    function importar_libreria_aplicacion($dir){
        $dir= PATHAPP . $dir . ".php";
        require_once $dir;
    }
    

    /**
     * Carga una libreria de la aplicacion pasando una direccion
     * @param string $dir
     */
    function importar_helper_framework($dir){
        $dir= PATHFRA . "helpers/" . $dir . ".php";
        require_once $dir;
    }
    

    /**
     * Carga un helper del framework pasando una direccion
     * @param string $dir
     */
    function importar_helper_aplicacion($dir){
        $dir= PATHAPP . $dir . ".php";
        require_once $dir;
    }
    

    /**
     * Carga un archivo de la aplicacion pasando una direccion
     * @param string $dir
     */
    function importar_archivo_aplicacion($dir){
        $dir= PATHAPP . $dir . ".php";
        require_once $dir;
    }
    
    
    /**
     * Carga un archivo que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function cargar_archivo_aplicacion($dir){
        $dir= PATHAPP . $dir;
        return file($dir);
    }
    
    /**
     * Carga un archivo de configuracion que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function cargar_archivo_configuracion_aplicacion($dir){
        $dir= PATHAPP . $dir;
        return parse_ini_file($dir);
    }
    
    
    /**
     * Carga un archivo que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function cargar_archivo_framework($dir){
        $dir= PATHFRA . $dir;
        return file($dir);
    }
    
    /**
     * Carga un archivo de configuracion que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function cargar_archivo_configuracion_framework($dir){
        $dir= PATHFRA . $dir;
        return parse_ini_file($dir);
    }
    
       
    /*
     * Recorre las librerias y analiza si carga o no la libreria en la determinada clase
     * Es llamado por el controlador en su construccion para cargar las librerias correspondientes
     * Esta funcion supone que la ibreria ya se encuentra importada
     */
    function cargar_librerias_modulo($objeto, $tipo){
        //Analiza las librerias del framework
        foreach ($GLOBALS['archivos_librerias'] as $libreria) { 
            if(isset($libreria['cargar_en'])){
                //Si esta seteada la variable cargar_en y contiene la definicion contralador carga la libreria
                if(strpos($libreria['cargar_en'], $tipo) !== FALSE){
                    $dir= $libreria['clase'];
                    $dir= explode("/", $dir);
                    $clase= $dir[count($dir) - 1];
                    agregar_instancia($clase, $objeto, $libreria['nombre']);
                }
            }
        }
        //Analiza las librerias de la aplicacion
        foreach ($GLOBALS['archivos_librerias_a'] as $libreria) {
            if(isset($libreria['cargar_en'])){
                //Si esta seteada la variable cargar_en y contiene la definicion contralador carga la libreria
                if(strpos($libreria['cargar_en'], $tipo) !== FALSE){
                    $dir= $libreria['clase'];
                    $dir= explode("/", $dir);
                    //Me quedo solo con el nombre de la clase y dejo todo lo demas
                    $clase= $dir[count($dir) - 1];
                    agregar_instancia($clase, $objeto, $libreria['nombre']);
                }
            }
        }
    }
    
    /*
     * Carga la instancia de objeto en una variable del objeto pasado como parametro
     * Supone que la clase ya se encuentra importada
     */
    function agregar_instancia($clase, $obj, $nombre = ""){
        if($nombre == ""){
            $nombre= $clase;
        }
        $obj->$nombre= new $clase();
    }
?>