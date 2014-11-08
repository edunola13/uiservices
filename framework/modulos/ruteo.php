<?php
    /**
     * Este modulo se encarga de realizar la carga del controlador que corresponda
     */

    // Carga la clase base de la que deben extender todos los filtros
    require_once PATHFRA . "clases/Controlador.php";
      
    
    //Recorre todos los controladores hasta que uno coincida con la URI actual
    foreach ($controladores as $controlador_esp) {
        //Analiza si el controlador mapea con la uri actual
        $mapea= mapea_url_actual($controlador_esp['url']);
        
        if($mapea){
            $dir= PATHAPP . $controlador_esp['ubicacion'] . "/" . $controlador_esp['clase'] . ".php";
            $controlador= NULL;
            //Analiza si existe el archivo
            if(file_exists($dir)){
                require_once $dir;
                $controlador= new $controlador_esp['clase']();
                //Le paso el parametro correspondiente a la uri, de los ( ) ni get ni post
                $controlador->parametros_uri= parametros_uri($controlador_esp['url']);
            }
            else{
                //Avisa que el archivo no existe
                mostrar_error("Error Controlador", "El controlador " . $controlador_esp['clase'] . " no existe");
            }
            
            //Saca el metodo HTPP y en base a eso hace una llamada al metodo correspondiente
            $metodo= $_SERVER['REQUEST_METHOD'];
            try{
                switch ($metodo) {
                    case "GET":
                        $controlador->doGet();
                        break;

                    case "POST":
                        $controlador->doPost();
                        break;

                    case "UPDATE":
                        $controlador->doUpdate();
                        break;

                    case "DELETE":
                        $controlador->doDelete();
                        break;
                    
                    case "OPTIONS":
                        $controlador->doOptions();
                        break;

                    default :
                        mostrar_error("Error Metodo HTTP", "El metodo $metodo HTTP no es soportado");
                }
            }
            catch (Exception $e){
                mostrar_error("Error ejecutando Metodo HTTP - " . $metodo, $e->getMessage());
            }
            
            //Sale del for para que no llame a otro controlador
            break;
        }            
    }
    //si ningun controlador mapeo avisa el problema
    if(! $mapea){
        mostrar_error_404();
    }
?>