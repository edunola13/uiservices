<?php
class ServerDefinition extends En_Controller{
    protected $tiwg;
    
    public function __construct() {
        parent::__construct();
        $this->twig= new Twig();
    }
    
    /**
     * Devuelve la definicion del servidor. Digamos todos sus componentes
     */
    public function doGet(){
        $config= Config::getInstance();
        $config->loadAllProjects();
        //Definicion del servidor
        $definition= "";
        
        foreach ($config->projects as $projectName => $datos) {
            $config->setActualProject($projectName);
            $projectConfig= $config->actualProjectConfig();
            foreach($projectConfig['components'] as $name => $folder){
                $def= component('ui_component', array("nombre" => $name));
                //Los saltos de linea ya lo realizo el componente
                $definition.= $projectName .'&component&'. $name . '=' . $def . PHP_EOL;
            }
            
            foreach($projectConfig['javascripts'] as $name => $folder){
                $folder.= '/';
                $folder= ltrim($folder, '/');
                $def= $this->twig->render("javascript/".$projectConfig['base'].'/'.$folder.$name.".html.twig");
                //Quito los saltos de linea                
                $def = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $def);
                $definition.= $projectName .'&javascript&'. $name . '=' . $def . PHP_EOL;
            }
            
            foreach($projectConfig['themes'] as $name => $folder){
                $folder.= '/';
                $folder= ltrim($folder, '/');
                $def= $this->twig->render("theme/".$projectConfig['base'].'/'.$folder.$name.".html.twig");
                //Quito los saltos de linea                
                $def = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $def);
                $definition.= $projectName .'&theme&'. $name . '=' . $def . PHP_EOL;
            }
        }
        
        $api= $this->request->param_get('api');
        $fileName= 'ServerDefinition-' . date('Y-m-d');
        if($api == 'PHP'){
            $fileName.= '.txt';
        }else{
            $fileName.= '.properties';
        }
        
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/x-download; "); 
        header("Content-Transfer-Encoding: binary");
        
        echo $definition;
    }
}

?>
