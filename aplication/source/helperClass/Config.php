<?php
class Config {
    private static $instance;
    public $configProjects;
    public $projects;
    public $nameActualProject;
    
    private function __construct() {
        $config= file_get_contents(PATHAPP . CONFIGURATION . 'projects.json');
        $this->configProjects= json_decode($config, TRUE);
    }
    
    public static function getInstance(){
        if(self::$instance == NULL){
            self::$instance= new Config();
        }
        return self::$instance;
    }
    
    public function defaultProject(){
        return $this->configProjects['default'];
    }
    
    public function setActualProject($name){
        if(isset($this->configProjects[$name])){
             $this->nameActualProject= $name;
        }
    }
    
    public function loadProject($name){
        if(!isset($this->projects[$name])){
            if(isset($this->configProjects[$name])){
                $config= file_get_contents(PATHAPP . CONFIGURATION . $name . '.json');
                $this->projects[$name]= json_decode($config, TRUE);
            }else{
                return 'ERROR';
            }
        }
        $this->nameActualProject= $name;
    }
    
    public function loadAllProjects(){        
        foreach ($this->configProjects as $project => $contenido) {
            if(!isset($this->projects[$project]) && $project != 'default'){
                $config= file_get_contents(PATHAPP . CONFIGURATION . $project . '.json');
                $this->projects[$project]= json_decode($config, TRUE);
            }
        }
    }
    
    public function actualProjectConfig(){
        return $this->projects[$this->nameActualProject];
    }
}

?>
