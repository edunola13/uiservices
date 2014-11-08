<?php
    /**
     * Este modulo realiza acciones de seguridad
     * Contiene funciones que son utilizadas por el framework
     * Con tiene funciones tambien que sirven para el usuario
     */

    /**
     * Funcion para codificar datos en md5
     * @param string $valor
     * @return string
     */
     function codificar_md5($valor){
         return md5($valor);
     }
     
     /**
      * Funcion para codificar datos en sha1
      * @param string $valor
      * @return string
      */
     function codificar_sha_1($valor){
         return sha1($valor);
     }
     
     /**
      * Funcion para codificar datos en md5 y sha1
      * @param string $valor
      * @return string
      */
     function codificar_md5_y_sha_1($valor){
         $valor= md5($valor);
         return sha1($valor);
     }
     
     /**
      * Simple filtro que saca las '' y "" para que no se pueda realizar xss
      * Ahi que mejorarlo
      * @param string $valor
      * @return string
      */
     function filtro_simple_xss($valor){
         $valor= str_replace('"','',$valor);
         return str_replace("'","",$valor);
     }
     
     /**
      * Realiza la limpieza de un string o conjunto de string llamando a la funcion filtro_xss
      * @param string o array[string] $valor
      * @return string o array[string]
      */
     function limpiar_variables($valor){
        if(is_array($valor)){
            foreach($valor as $key => $val) {
                $valor[$key] = limpiar_variables($val);
            }
        }
        else{
            $valor= filtro_simple_xss($valor);
        }
        return $valor;
     }          
?>