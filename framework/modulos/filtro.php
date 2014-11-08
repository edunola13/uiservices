<?php
    /*
     * Este modulo se encarga de realizar la carga de los filtros que corresponda
     */

    // Carga la clase base de la que deben extender todos los filtros
    require_once PATHFRA . "clases/Filtro.php";
    
    /**
     * Analiza los filtros correspondientes y ejecuta los que correspondan
     * @param array[array] $filtros
     */
    function realizar_filtrado($filtros){
        //Analizo los filtros y los aplico en caso de que corresponda
        foreach ($filtros as $filtro_esp) {
            $filtrar= mapea_url_actual($filtro_esp['filtra']);

            //Si debe filtrar carga el filtro correspondiente y realiza el llamo al metodo filtrar()
            if($filtrar){
                $dir= PATHAPP . $filtro_esp['ubicacion'] . "/" . $filtro_esp['clase'] . ".php";
                //Analiza si existe el archivo
                if(file_exists($dir)){
                    require_once $dir;
                    $filtro= new $filtro_esp['clase']();
                    //Analiza si existe el metodo filtrar
                    if(method_exists($filtro, "filtrar")){
                        echo $filtro->filtrar();
                    }
                    else{
                        mostrar_error("Error Filtro", "El filtro " . $filtro_esp['clase'] . " no cuenta con el metodo filtrar()");
                    }
                }
                else{
                    mostrar_error("Error Filtro", "El filtro " . $filtro_esp['clase'] . " no existe");
                }
            }

        }
    }
?>