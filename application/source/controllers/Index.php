<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

class Index extends Http\En_Controller{
    public function __construct() {        
        parent::__construct();
    }
    
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        echo 'UI Services!!!';
    }
}