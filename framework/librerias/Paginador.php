<?php

/**
 * Description of Paginador
 *
 * @author Enola
 */
class Paginador {
    public $cantidad_por_pagina;
    public $cantidad_total;
    public $pagina_actual;
    
    public function __construct($cantidad_por_pagina, $cantidad_total, $pagina_actual) {
        $this->cantidad_por_pagina= $cantidad_por_pagina;
        $this->cantidad_total= $cantidad_total;
        $this->pagina_actual= $pagina_actual;
    }
    
    /**
     * Retorna la cantidad de paginas que hay
     * @return int
     */
    public function cantidad_de_paginas(){
        $cantidad= $this->cantidad_total / $this->cantidad_por_pagina;
        if(is_int($cantidad)){
            return $cantidad;
        }
        else{
            $cantidad_int= intval($cantidad);
            if($cantidad_int > $cantidad){
                return $cantidad_int;
            }
            else{
                return $cantidad_int + 1;
            }
        }
    }
    
    /**
     * Retorna la posicion del elemento de inicio de la pagina actual.
     * Empieza de 0.
     * @return int
     */
    public function posicion_elemento_inicio(){
        return ($this->cantidad_por_pagina * $this->pagina_actual) - $this->cantidad_por_pagina;
    }
    
    /**
     * Retorna la posicion del elemento de fin de la pagina actual.
     * Empieza de 0.
     * @return int
     */
    public function posicion_elemento_fin(){
        if($this->cantidad_de_paginas() == $this->pagina_actual){
            return $this->cantidad_total - 1;
        }
        else{
            return $this->posicion_elemento_inicio() + $this->cantidad_por_pagina - 1;
        }
    }
    
    /**
     * Retorna la pagina anterior o null en caso de que no haya anterior
     * @return int
     */
    public function pagina_anterior(){
        if($this->pagina_actual > 1){
            return $this->pagina_actual - 1;
        }
        else{
            return NULL;
        }
    }
    
    /**
     * Retorna la pagina siguiente o null en caso de que no haya siguiente
     * @return int
     */
    public function pagina_siguiente(){
        if($this->pagina_actual < $this->cantidad_de_paginas()){
            return $this->pagina_actual + 1;
        }
        else{
            return NULL;
        }
    }

}

?>