<?php
    /**
     * Importa todo lo necesario para manejar las BD
     * Contiene funciones para manejar la BD por el framework y por el usuario
     */

    //Clase de la que deben extender todas las clases que quieran manejar la BD con el framework implicitamente
    require_once PATHFRA . "clases/BaseDatos.php";

    
    /**
     * Funcion que conecta a una BD y retorna la conexion
     * @return \PDO
     */
    function conectar_bd(){
        //Si ya esta seteada quiere decir que ya se conecto a la BD en otro momento
        if(! isset($GLOBALS['gbd'])){
            //Leo archivo de configuracion de BD
            if(defined("JSON_CONFIG_BD")){
                $json_basededatos= file_get_contents(PATHAPP . CONFIGURACION . JSON_CONFIG_BD);
            }
            else {
                mostrar_error("Base de Datos", "No se ha especificado el archivo de configracion de la base de datos", "error_bd");
            }
            $config_bd= json_decode($json_basededatos, TRUE);
            //Consulta la bd actual
            $opcion= $config_bd['bd_actual'];
            //Cargo las opciones de la bd actual
            $cbd= $config_bd[$opcion];

            //Abro una conexion
            try {
                // 5.3.5 o < y luego 5.3.6 o >
                //Cuidado que charset=utf8 puede no funcar para versiones viejas y luego en opciones
                //superiores habria q usar PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                //Por ahora uso las 2 y anda la que anda
                //Creo el dsn
                $dsn=  $cbd['driverbd'].':host='.$cbd['hostname'].';dbname='.$cbd['basededatos'].';charset='.$cbd['charset'];
                //Abro la conexion
                
                $gbd = new PDO($dsn, $cbd['usuario'], $cbd['clave'], array(PDO::ATTR_PERSISTENT => $cbd['persistente'], PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$cbd['charset']));
                $gbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                //Guarda la conexion en un variable global
                $GLOBALS['gbd']= $gbd;
                //Retorno la conexion 
                return $gbd;
            } 
            catch (PDOException $e) {
                mostrar_error("Error Conexion", $e->getMessage(), "error_bd");
            }
        }
        else{
            return $GLOBALS['gbd'];
        }
    }
    
    /**
     * Cierra la conexion a la BD
     */
    function cerrar_conexion_bd(){
        if(isset($gbd)){
            unset($gbd);
        }
        if(isset($GLOBALS['gbd'])){
            unset($GLOBALS['gbd']);
        }
    }
?>