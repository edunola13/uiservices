<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

class Server extends Http\En_Controller{
    public $config;
    public $tiwg;
    
    public function __construct() {        
        parent::__construct();
        $this->injectDependency($this->context->app, 'twig', 'twig');
    }
    
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){
        $this->config->loadAllProjects();
        //Definicion del servidor
        $definition= '';
        $projects= $this->config->projects;
        if($request->getParam('projects') != NULL){
            $getProjects= array_flip(explode(',', $request->getParam('projects')));
            $projects= array_intersect_key($projects, $getProjects);
        }
        //Solo me quedo con las claves que es lo que realmente me interesa
        $projects= array_keys($projects);
        foreach ($projects as $projectName) {
            $this->config->setActualProject($projectName);
            $projectConfig= $this->config->actualProjectConfig();
            foreach($projectConfig['components'] as $name => $folder){
                $def= $this->component('ui-component', array('name' => $name), NULL, TRUE);
                //Los saltos de linea ya lo realizo el componente
                $definition.= $projectName .'&component&'. $name . '=' . $def . PHP_EOL;
            }
            
            foreach($projectConfig['javascripts'] as $name => $folder){
                $folder.= '/';
                $folder= ltrim($folder, '/');
                $def= $this->twig->render("javascript/".$projectConfig['base'].'/'.$folder.$name.".html.twig", array('view' => new Enola\Support\View()));
                //Quito los saltos de linea                
                $def = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $def);
                $definition.= $projectName .'&javascript&'. $name . '=' . $def . PHP_EOL;
            }
            
            foreach($projectConfig['themes'] as $name => $folder){
                $folder.= '/';
                $folder= ltrim($folder, '/');
                $def= $this->twig->render("theme/".$projectConfig['base'].'/'.$folder.$name.".html.twig", array('view' => new Enola\Support\View()));
                //Quito los saltos de linea                
                $def = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $def);
                $definition.= $projectName .'&theme&'. $name . '=' . $def . PHP_EOL;
            }
        }
        
        $api= strtoupper($this->getUriParam('api'));
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
