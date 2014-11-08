<?php
    /**
     * Realiza la configuracion completa del sistema y empieza a delegar el requerimiento del cliente a los modulos del
     * framework y a los del cliente
     */
      
    /**
     * Lee archivo configuracion.json donde se encuentra toda la configuracion de variables, filtros, controladores, 
     * librerias, helpers, etc.
     */
    $json_configuracion= file_get_contents($path_aplicacion . '/configuracion.json');
    $config= json_decode($json_configuracion, true);
    
    if(! is_array($config)){
        //Arma un respuesta de error de configuracion.
        //No realiza el llamado a funcions de error porque todavia no se cargo el modulo de errores
        $cabecera= "Error de Configuracion";
        $mensaje= 'El archivo configuracion.json no esta disponible, no cuenta con ningun atributo definido o se encuentra mal escrito';
        require_once $path_aplicacion . 'errores/error_general.php';
        //Cierra la aplicacion
        exit;
    }
    
    //Define si muestra o no los errores y en que nivel de detalle dependiendo en que fase se encuentre la aplicacion
    switch ($config['ambiente']){
        case 'desarrollo':
            error_reporting(E_ALL);
            define("ERROR", "todos");
            break;
	
        case 'produccion':
            error_reporting(0);
            define("ERROR", "ninguno");
            break;

        default:
            //No realiza el llamado a funcions de error porque todavia no se cargo el modulo de errores
            $cabecera= "Error de Configuracion";
            $mensaje= 'El ambiente de la aplicacion no se ha definido';
            require_once $path_aplicacion . 'errores/error_general.php';
            exit;
    }
    
    //Carga la clase Rendimiento
    require_once $path_framework . "clases/Rendimiento.php";
    //Analiza si calcula el tiempo que tarda la aplicacion en ejecutarse o no
    $rendimiento= NULL;
    if($config['calcular_tiempo'] == "TRUE" || $config['calcular_tiempo'] == "true"){
        //Incluye la clase Rendimiento 
        $rendimiento= new Rendimiento();
        $rendimiento->iniciar_calculo();
    }

    //Seteo la codificacion de caracteres, casi siempre es o debe ser UTF-8
    ini_set('default_charset', $config['charset']);
    
    // Define las constantes del sistema
    // BASE_URL: Base url de la aplicacion - definida por el usuario en el archivo de configuracion
    define("BASEURL", $config['base_url']);
    
    //CONFIGURACION: carpeta base de configuracion - definida por el usuario en el archivo de configuracion
    define("CONFIGURACION", $config['configuracion']);
    
    //JSON_CONFIG_BD: archivo de configuracion para la base de datps
    //Si el usuario definio que va a tener bd en el archivo de configuracion, guarda el archivo de configuracion de la BD
    if(isset($config['basededatos']['configuracion'])){
        define("JSON_CONFIG_BD", $config['basededatos']['configuracion']);
    }
    
    // PATHFRA: direccion de la carpeta de la aplicacion - definida en index.php
    define("PATHFRA", $path_framework);
    
    // PATHAPP: direccion de la carpeta de la aplicacion - definida en index.php
    define("PATHAPP", $path_aplicacion);
     
    
    /*
     * Creacion de variables globales
     */
    
    //Creo variable global con la configuracion de Internacionalizacion
    if(isset($config['internacionalizacion'])){
        $GLOBALS['internacionalizacion']= $config['internacionalizacion'];
    }
    
    //Creo la variable global con la configuracion de librerias
    $GLOBALS['archivos_librerias']= $config['librerias'];
    
    //Creo la variable global con la configuracion de librerias aplicacion
    $GLOBALS['archivos_librerias_a']= $config['librerias_aplicacion'];
    
    /*
     * Fin creacion de variables globales
     */
    
    /*
     * Carga de modulos obligatorios para que el framework trabaje correctamente
     */
    
    //Carga del modulo errores
    require_once PATHFRA . "modulos/errores.php";
    //Define un manejador de excepciones - definido en el modulo errores
    set_error_handler("_manejador_de_errores");
    //Define un manejador de fin de cierre - definido en el modulo de errores
    register_shutdown_function('_manejador_shutdown'); 
        
    //Carga de modulo URL-URI
    require_once PATHFRA . "modulos/url_uri.php";
    //Define la uri de la aplicacion y la setea como una variable estatica
    definir_uri_aplicacion();
        
    //Carga de modulo para carga de archivos
    require_once PATHFRA . "modulos/cargar_archivos.php";
    
    //Carga de modulo con funciones para la vista
    require_once PATHFRA . "modulos/vista.php";

    //Carga de modulo de seguridad
    require_once PATHFRA . "modulos/seguridad.php";
    
    //Carga de modulo de informacion
    require_once PATHFRA . "modulos/informacion.php";
    
    /*
     * Fin carga de modulos obligatorios
     */  
    
    
    /**
     * Analiza el paso de un error HTTP
     */
    capturar_error_servidor();
    /**
     * Fin
     */
    
    
    /**
     * Realiza la importacion de librerias y helpers indicados en el archivo de configuracion  
     */
        
    /**
     * Cargo todas las librerias del framework que se cargaran automaticamente
     */
    //Leo las librerias de la variable config
    $archivos_librerias= $config['librerias'];
    //Recorro de a una las librerias y las importo
    foreach ($archivos_librerias as $libreria) {
        $dir= $libreria['clase'];
        importar_libreria_framework($dir);
    }  
    
    /*
     * Cargo todas las librerias particulares de la aplicacion que se cargaran automaticamente
     */
    //Leo las librerias de la variable config
    $archivos_librerias_a= $config['librerias_aplicacion'];
    //Recorro de a una las librerias y las importo
    foreach ($archivos_librerias_a as $libreria) {
        //$libreria['clase'] tiene la direccion completa desde PATHAPP, no solo el nombre
        $dir= $libreria['clase'];
        importar_libreria_aplicacion($dir);
    }
    
    /*
     * Cargar todos los helpers/funciones del framewrok que se cargaran automaticamente
     */
    //Leo los helpers de la variable config
    $archivos_helpers= $config['helpers'];
    //Recorro de a una las variables y las importo
    foreach ($archivos_helpers as $helper) {
        $dir= $helper['direccion'];
        importar_helper_framework($dir);
    }
    
    /*
     * Cargar todos los helpers/funciones particulares de la aplicacion que se cargaran automaticamente
     */
    //Leo los helpers de la variable config
    $archivos_helpers_a= $config['helpers_aplicacion'];
    //Recorro de a una las variables y las importo
    foreach ($archivos_helpers_a as $helper) {
        $dir= $helper['direccion'];
        importar_helper_aplicacion($dir);
    }
   
    /**
     * Fin carga de librerias y helpers 
     */
    
    /**
     * Carga de archivo BD y realiza la conexion a la BD al inicio si es necesario
     */
    require_once PATHFRA . 'modulos/basededatos.php';
    if(isset($config['basededatos']['conexion_encarga'])){
        if($config['basededatos']['conexion_encarga'] == "TRUE" || $config['basededatos']['conexion_encarga'] == "true"){
            conectar_bd();
        }
    }
      
    /**
     * Fin carga de BD
     */
       
    /**
     * Realiza la llamada de filtros y controlador correspondientes
     */
      
    /**
     * Carga el modulo de Filtro
     */
    require_once PATHFRA . "modulos/filtro.php";
    /**
     * Lee los filtros que se deben ejecutar antes del procesamiento de la variable config y delega trabajo a archivo filtros.php
     * En caso de que no haya filtros asignados no delega ningun trabajo
     */
    $filtros= $config['filtros'];
    if(count($filtros) > 0){
        realizar_filtrado($filtros);
    }
    
    
    
    /**
     * Almacena la definicion de componentes en una variable global y analiza si carga el modulo componente
     */
    
    //Leo las librerias de la variable config
    $componentes= $config['componentes'];
    if(count($componentes) > 0){
        //La guarda como global para que luego pueda ser utilizada
        $GLOBALS['componentes']= $componentes;
        //Cargo el modulo componente
        require_once PATHFRA . "modulos/componente.php";
    }
    
    
    
    /**
     * Lee los controladores de la variable config y delegar trabajo a ruteo.php
     * En caso de que no haya controladores avisa del error
     */
    $controladores= $config['controladores'];
    if(count($controladores) > 0){
        require_once PATHFRA . "modulos/ruteo.php";
    }
    else{
        mostrar_error("Error Controlador", "No se ha definido ningun controlador");
    }
         
    /**
     * Lee los filtros que se deben ejecutar despues del procesamiento de la variable config y delega trabajo a archivo filtros.php
     * En caso de que no haya filtros asignados no delega ningun trabajo
     */
    $filtros_despues= $config['filtros_despues_procesamiento'];
    if(count($filtros_despues) > 0){
        realizar_filtrado($filtros_despues);
    }
      
    /**
     * Fin llamada de filtros y controlador
     */
      
    /**
     * Desconecto conexion a BD
     */
    cerrar_conexion_bd();
        
    /*
     * Si se esta calculando el tiempo, realiza el calculo y envia la respuesta
     */
    if($rendimiento != NULL){
        $rendimiento->terminar_calculo();
        $mensaje= "El tiempo de ejecucion de la aplicacion es: " . $rendimiento->tiempo_consumido();
        $titulo= "Rendimiento";
        //Muestra la informacion al usuario
        mostrar_informacion($titulo, $mensaje);
    }
?>