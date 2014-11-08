<?php
    /**
     * Importa todo lo necesario para manejar los componentes
     * Contiene funciones para manejar los componentes por el framework y por el usuario
     */

    require_once PATHFRA . "clases/Componente.php";

    /**
     * Ejecuta el metodo renderizar de un componente
     * @param type $nombre
     * @param type $parametros
     */ 
    function ejecutar_componente($nombre, $parametros = NULL){
        $componente= NULL;
        foreach ($GLOBALS['componentes'] as $componente_esp) {
            if($nombre == $componente_esp['nombre']){
                importar_archivo_aplicacion($componente_esp['ubicacion'] . "/" . $componente_esp['clase']);
                $componente= new $componente_esp['clase']();
            }
        }
        if($componente != NULL){
            //Analiza si existe el metodo filtrar
            if(method_exists($componente, "renderizar")){
                if($parametros == NULL){
                    return $componente->renderizar();
                }
                else{
                    return $componente->renderizar($parametros);
                }
            }
            else{
                mostrar_error("Error Componente", "El componente " . $componente_esp['nombre'] . " no cuenta con el metodo renderizar()");
            }          
        }
        else{
            mostrar_error("Error Componente", "El componente $nombre no existe");
        }
    }

?>
