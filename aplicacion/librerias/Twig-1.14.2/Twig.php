<?php

/**
 * Libreria que realiza el manejo de la vista con Twig
 *
 * @author Enola
 */
class Twig {
    private $loader; // Instance of Twig_Loader_Filesystem
    private $environment; // Instance of Twig_Environment
    
    /**
     * Realiza toda la configuracion con Twig para que la libreria quede lista para ser usada por el usuario
     */
    public function __construct(){
        //Configuracion Twig
        $json_twig= file_get_contents(PATHAPP . CONFIGURACION . "twig.json");
        $config_twig= json_decode($json_twig, TRUE);
        
        require_once 'lib/Twig/Autoloader.php';
        // Twig's autoloader will take care of loading required classes
        Twig_Autoloader::register();
        
        //$config_twig esta definida en twig.php
        $this->loader = new Twig_Loader_Filesystem(PATHAPP . $config_twig['template_dir']);
        
        if($config_twig['ambiente'] == "produccion"){
            //Para produccion
            $this->environment = new Twig_Environment($this->loader, array("cache" => PATHAPP . $config_twig['cache_dir']));
        }
        else{
            //Para Desarrollo, no usa cache
            $this->environment = new Twig_Environment($this->loader);
        }     
        
        //Cargamos funciones de la vista a Twig
        $this->environment->addFunction('base', new Twig_Function_Function('base'));
        $this->environment->addFunction('base_locale', new Twig_Function_Function('base_locale'));
        $this->environment->addFunction('locale_uri', new Twig_Function_Function('locale_uri'));
        $this->environment->addFunction('reemplazar', new Twig_Function_Function('reemplazar'));
        $this->environment->addFunction('reemplazar_blancos', new Twig_Function_Function('reemplazar_blancos'));
        $this->environment->addFunction("componente", new Twig_Function_Function('componente'));
        $this->environment->addFunction('i18n', new Twig_Function_Function('i18n'));
        $this->environment->addFunction('i18n_cambiar_locale', new Twig_Function_Function('i18n_cambiar_locale'));
        $this->environment->addFunction("i18n_valor", new Twig_Function_Function('i18n_valor'));
        $this->environment->addFunction("i18n_locale", new Twig_Function_Function('i18n_locale'));
    }
    
    /**
     * Carga una vista correspondiente con sus correspondientes datos
     * @param string $templateFile
     * @param array $variables
     * @return VISTA
     */
    public function render($templateFile, array $variables = NULL){
        if($variables == NULL){
            return $this->environment->render($templateFile);
        }
        else{
            return $this->environment->render($templateFile, $variables);
        }
    }
  
    /**
     * Carga un template correspondiente
     * @param string $templateFile
     * @return TEMPLATE
     */
    public function loadTemplate($templateFile){
        return $this->environment->loadTemplate($templateFile);
    }
}

?>