<?php
class JavaScript extends En_Controller{
    private $twig;
    
    public function __construct() {
        parent::__construct();
        $this->twig= new Twig();
    }    
    
    public function doGet(){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        /**
         * Seteo el proyecto 
         */
        $proyecto= 'bootstrap3';
        if($this->request->param_get("proyecto") != NULL){
            $proyecto= $this->request->param_get("proyecto");
        }        
        $nombre= $this->request->param_get("nombre");        
        if($nombre == NULL){
            $nombre= "base";
        }        
        echo $this->twig->render("javascript/".$proyecto.'/'.$nombre.".html.twig");
    }
}

?>
