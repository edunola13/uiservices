<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

class Test extends Http\En_Controller{   
    public $twig;
    public $sons;    
    public $datos;
    
    public function __construct() {
        parent::__construct();
        $this->injectDependency($this->context->app, 'twig', 'twig');
    }
    
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){
        if($this->getUriParam(0) == NULL){
            $configForm= array('method' => '#', 'label' => 'Formulario');
            $this->loadView('test/test1', array('configForm' => $configForm));
        }else{
            
        }        
    }
}