<?php
    /*
     * Maneja los errores del framework
     */
    
    /**
     * Funcion para manejar los errores php. 
     * Esta se superpone a la propia de php cuando es seteada en el nucleo.php
     * @param $nivel_error
     * @param string $mensaje
     * @param string $archivo
     * @param int $linea
     * @return boolean
     */
    function _manejador_de_errores($nivel_error, $mensaje, $archivo, $linea){
        if (!(error_reporting() & $nivel_error)) {
            //Agrega el Log
            escribir_log($mensaje, "nivel_error: ". $nivel_error);
            // Segun el nivel de error veo si agarro o no la excepcion. si entra aca no hago nada
            return;
        }

        //Analizo el error que se produjo y aviso del mismo.
        //Segun el error termino el flujo de ejecucion o continua
        switch ($nivel_error) {
            case E_USER_ERROR:
                mostrar_error_php('Error', $nivel_error, $mensaje, $archivo, $linea);
                exit(1);
                break;

            case E_USER_WARNING:
                mostrar_error_php('Warning', $nivel_error, $mensaje, $archivo, $linea);
                break;

            case E_USER_NOTICE:
                mostrar_error_php('Notice', $nivel_error, $mensaje, $archivo, $linea);
                break;

            default:
                mostrar_error_php('Desconocido', $nivel_error, $mensaje, $archivo, $linea);
                break;
        }

        /* No ejecutar el gestor de errores interno de PHP */
        return true;      
    }
    
    /**
     * Funcion que se va a ejecutar en el cierre de ejecucion de la aplicacion.
     * La vamos a utilizar para manejar los errores fatales
     */
    function _manejador_shutdown(){
        if(!is_null($e = error_get_last())){
            //Se podria agregar mas errores en el IF, ver set error handler en PHP para ver cuales no son manejados con esa funcion
            //Si no son manejados con esa funcion todos cierran el programa directamente
            if($e['type'] == E_ERROR || $e['type'] == E_PARSE || $e['type'] == E_STRICT){
                if(!(error_reporting() & $e['type'])){
                    escribir_log($e['message'], $e['type']);
                }
                else{
                    mostrar_error_php('Error Fatal - Parse - Strict', $e['type'], $e['message'], $e['file'], $e['line']);
                }
            }
        }  
    }
    
    /**
     * Funcion que es llamada para crear una respuesta de error php - usada por el manejador de errores definido por el framework
     * @param string $tipo_error
     * @param $nivel_error
     * @param string $mensaje
     * @param string $archivo
     * @param int $linea
     */
    function mostrar_error_php($tipo_error, $nivel_error, $mensaje, $archivo, $linea){
        escribir_log($mensaje, $tipo_error);
        require_once PATHAPP . "errores/error_php.php";
    }
    
    /**
     * Funcion que es llamada para crear un respuesta de error 404
     * Usada por el framework y/o el usuario
     */
    function mostrar_error_404(){
        $cabecera= "404 Pagina no Encontrada";
        $mensaje= "La pagina que solicitaste no existe";
        mostrar_error($cabecera, $mensaje, "error_404", 404);
    }
    
    /**
     * Funcion que es llamada para crear una respuesta de error general
     * Usada por el framework y/o el usuario
     * @param string $cabecera
     * @param string $mensaje
     * @param string $template
     * @param int $codigo_error
     */
    function mostrar_error($cabecera, $mensaje, $template = "error_general", $codigo_error = 500){
        escribir_log("error_general", $mensaje);
        set_estado_header($codigo_error);
        require_once PATHAPP . "errores/" . $template . ".php";
    }
    
    /**
     * Crea o abre un archivo de log y escribe el error correspondiente
     * @param String $cadena
     * @param String $tipo
     */
    function escribir_log($cadena, $tipo){
	$arch = fopen(PATHAPP . "logs/log.txt", "a+"); 
	fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".$_SERVER['REMOTE_ADDR']." ".
                   " - $tipo ] ".$cadena."\n");
        fwrite($arch, "----------\n");
	fclose($arch);
    }
    
    
    /**
     * Analiza si se envia a traves de un parametro get un error HTTP
     */
    function capturar_error_servidor(){
        if(isset($_GET['error_apache_enola'])){
            //Cargo el archivo con los errores
            $errores= cargar_archivo_framework('informacion/erroresHTTP.ini');
            $errores= parse_properties($errores);
            //Muestro el error correspondiente
            mostrar_error("Error " . $_GET['error_apache_enola'], $errores[$_GET['error_apache_enola']] , "error_general", $_GET['error_apache_enola']);
            //No continuo la ejecucion
            exit;
        }
    }
?>
