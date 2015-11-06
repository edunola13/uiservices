<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

class JavaScript extends Http\En_Controller{
    public $config;
    public $twig;
    
    public function __construct() {        
        parent::__construct();
        $this->injectDependency($this->context->app, 'twig', 'twig');
    }
    
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        if($this->getUriParam('project') != NULL){
            $this->config->loadProject($this->getUriParam('project'));
            $name= "base";
            if($this->getUriParam('name') != NULL){
                $name= $this->getUriParam('name');
            }
            $project= $this->config->actualProjectConfig();
            $folderView= $project['javascripts'][$name] . '/';
            //Le quito el "/" en caso de que no haya carpeta
            $folderView= ltrim($folderView, '/');
            $base= $project['base'];
        
            echo $this->twig->render("javascript/".$base.'/'.$folderView.$name.".html.twig", array('view' => new Enola\Support\View()));
        }
    }
}
