<?php
/**
 * Description of Test
 *
 * @author Usuario_2
 */
class Test extends En_Controller{    
    protected $twig;
    protected $hijos;
    protected $config;
    protected $datos;
    
    public function __construct() {
        parent::__construct();
        $this->twig= Twig::getInstance();
    }
    
    /**
     * Devuelve la definicion de un componente 
     */
    public function doGet(){
        $this->config= array('label' => 'Formulario');
        $this->load_view('test');
    }
}

?>
