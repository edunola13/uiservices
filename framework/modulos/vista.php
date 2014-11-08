<?php
    /*
     * Este modulo tiene funciones utiles para usar en la vista de la aplicacion
     */
       
    /**
     * Retorna la baseurl
     * @return string
     */
    function base(){
        return BASEURL;
    }
    
    /**
     * Retorna la base url con el locale actual
     * @return string
     */
    function base_locale(){
        return BASEURL_LOCALE;
    }
    
    /**
     * Retorna el locale actual de la url
     * @return string
     */
    function locale_uri(){
        return LOCALE_URI;
    }

    /**
     * reemplaza $por por $reemplazar en el string $string
     * @param string $reemplazar
     * @param string $por
     * @param string $string
     * @return string
     */
    function reemplazar($reemplazar, $por, $string){
        return str_replace($por, $reemplazar, $string);
    }
    
    /**
     * Quita los blancos del string por -
     * @param string $string
     * @return string
     */
    function reemplazar_blancos($string){
        return str_replace(" ", "-", $string);
    } 
    
    /**
     * Realiza el llamado a la funcion que ejecuta el metodo renderizar del componente
     * @param type $nombre
     * @param type $parametros
     */
    function componente($nombre, $parametros = NULL){
        //Llama a la funcion que ejecuta el componente definido en el modulo Componente
        return ejecutar_componente($nombre, $parametros);
    }
    
    /**
     * Carga un archivo de internacionalizacion. Si no se especifica el locale carga el archivo por defecto, si no le agrega el locale pasado
     * @param type $archivo
     * @param type $locale
     */
    function i18n($archivo, $locale = NULL){
        $archivo_cargado= NULL;
        if($locale == NULL){
            $archivo_cargado= cargar_archivo_aplicacion($archivo . ".ini");
            $archivo_cargado= parse_properties($archivo_cargado);
            $GLOBALS["locale"]= "Por Defecto";
        }
        else{
            $archivo_cargado= cargar_archivo_aplicacion($archivo . "_$locale" . ".ini");
            $archivo_cargado= parse_properties($archivo_cargado);
            $GLOBALS["locale"]= $locale;
        }
        $GLOBALS["archivo_lenguaje"]= $archivo_cargado;
        $GLOBALS["archivo"]= $archivo;
    }
    
    /**
     * Cambia el archivo de internacionalizacion cargado. Lo cambia segun el locale pasado
     * @param type $locale
     */
    function i18n_cambiar_locale($locale){
        if(isset($GLOBALS["archivo"])){
            i18n($GLOBALS["archivo"], $locale);
        }
        else{
            mostrar_error("Error I18n", "Antes de llamar a i18n_cambiar_locale es necesario llamar a i18n");
        }
    }
    
    /**
     * Devuelve el valor segun el archivo de internacionalizacion que se encuentre cargado
     * @param type $clave
     * @return type
     */
    function i18n_valor($clave, $parametros = NULL){
        if(isset($GLOBALS['archivo_lenguaje'])){
            if(isset($GLOBALS["archivo_lenguaje"][$clave])){
                $mensaje= $GLOBALS["archivo_lenguaje"][$clave];
                
                //Analiza si se pasaron parametros y si se pasaron cambia los valores correspondientes
                if($parametros != NULL){
                    foreach ($parametros as $key => $valor) {
                        $mensaje= str_replace(":$key", $valor, $mensaje);
                    }
                }
                
                return $mensaje;
            }
        }
        else{
            mostrar_error("Error I18n", "No se ha especificado ningun archivo de I18n, para hacerlo ejecute la funcion i18n");
        }
    }
    
    /**
     * Retorna el locale configurado para el contenido internacionalizado
     */
    function i18n_locale(){
        if(isset($GLOBALS["locale"])){
            return $GLOBALS["locale"];
        }
        else{
            return "Por Defecto";
        }
    }
    
    /**
     * Este proceso analiza de a una las lineas del archivo de internacionalizacion usado. En este caso ini file y me arma lo que seria
     * un array asociativo clave valor en base a la linea.
     * Codigo descargado de: http://blog.rafaelsanches.com/2009/08/05/reading-java-style-properties-file-in-php/
     * @param type $lineas
     * @return type
     */
    function parse_properties($lineas) {
        $isWaitingOtherLine = false;
        foreach($lineas as $i=>$linea) {
            if(empty($linea) || !isset($linea) || strpos($linea,"#") === 0){
                continue;
            }

            if(!$isWaitingOtherLine) {
                $key = substr($linea,0,strpos($linea,'='));
                $value = substr($linea,strpos($linea,'=') + 1, strlen($linea));
            }
            else {
                $value .= $linea;
            }

            /* Check if ends with single '\' */
            if(strrpos($value,"\\") === strlen($value)-strlen("\\")) {
                $value = substr($value, 0, strlen($value)-1)."\n";
                $isWaitingOtherLine = true;
            }
            else {
                $isWaitingOtherLine = false;
            }
            $result[$key] = $value;
            unset($lineas[$i]);
        }

        return $result;
   }
?>