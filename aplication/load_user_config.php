<?php
/**
 * En este script el usuario realizara todas las configuraciones que crea necesaria para qu su aplicacion funciones correctamente
 * Todo lo que se ubique aca se ejecutara despúes de cargar las librerias y realizar la conexion a la BD (si se ha solicitado)
 * Y antes de empezar a trabajar el requerimiento HTTP, digamos antes de que se ejecuten los filtros, controladores, etc. 
 */

/*
 * Para que si haya un error devuelva error 500 y las APIs no muestren contenido erroneo
 */
    function _error_handler2($level, $message, $file, $line){
        set_estado_header(500);
        // No ejecutar el gestor de errores interno de PHP
        return true;      
    }
    function _shutdown2(){
        if(!is_null($e = error_get_last())){
            //Se podria agregar mas errores en el IF, ver set error handler en PHP para ver cuales no son manejados con esa funcion
            //Si no son manejados con esa funcion todos cierran el programa directamente
            if($e['type'] == E_ERROR || $e['type'] == E_PARSE || $e['type'] == E_STRICT){
                set_estado_header(500);
            }
        }        
    }    
    set_error_handler('_error_handler2');
    register_shutdown_function('_shutdown2');
?>
