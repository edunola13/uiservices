<?php

/**
 * Description of Index
 *
 * @author Enola
 */

class Index extends En_Controller{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function doGet(){
        //Modifico el Header
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        
        echo 'UI Services!!!';
    }
}

?>
