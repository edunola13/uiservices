<?php

/**
 * Clase utilizada para calcular el rendimiento de la aplicacion-framework
 *
 * @author Enola
 */
class Rendimiento {
    protected $tiempoInicio;
    protected $tiempoFin;
    
    /**
     * Constructor
     */
    public function __construct() {
        
    }
    
    /**
     * Inicial el calculo del tiempo para luego poder terminar y calcular el tiempo
     */
    public function iniciar_calculo(){
        //Guarda el tiempo actual en segundos
        $this->tiempoInicio = microtime(TRUE);
    }
    
    /**
     * Finaliza el calculo del tiempo
     */
    public function terminar_calculo(){
        //Guarda el tiempo actual en segundos
        $this->tiempoFin = microtime(TRUE);
    }
    
    /**
     * Calcula el tiempo consumido entre el inicio fin de calculo
     * @return float o NULL
     */
    public function tiempo_consumido(){
        if(isset($this->tiempoInicio) && isset($this->tiempoFin)){
            return $this->tiempoFin - $this->tiempoInicio;
        }
        else{
            if(! isset($this->tiempoInicio)){
                echo "No se ha iniciado el calculo del tiempo";
            }
            else{
                echo "No se ha finalizado el calculo del tiempo";
            }
        }
    }
    
}
?>