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
        
        //Cargo la configuracion y la configuracion para el proyecto default o sleccionado
        $config= Config::getInstance();
        $proyecto= $config->defaultProject();
        if($this->request->param_get("proyecto") != NULL){
            $proyecto= $this->request->param_get("proyecto");
        }
        $config->loadProject($proyecto);
               
        $nombre= $this->request->param_get("nombre");        
        if($nombre == NULL){
            $nombre= "base";
        }
        
        $project= $config->actualProjectConfig();
        $folderView= $project['javascripts'][$nombre] . '/';
        //Le quito el "/" en caso de que no haya carpeta
        $folderView= ltrim($folderView, '/');
        $base= $project['base'];
        
        echo $this->twig->render("javascript/".$base.'/'.$folderView.$nombre.".html.twig");
    }
}

?>
