<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

class Component extends Http\En_Controller{
    public $config;
    
    public function __construct() {        
        parent::__construct();
    }
    
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        if($this->getUriParam('project') != NULL && $this->getUriParam('name') != NULL){
            $this->config->loadProject($this->getUriParam('project'));
            echo $this->component('ui-component', array('name' => $this->getUriParam('name')), NULL, TRUE);
        }
    }
}