<?php

$config['base_url']= 'http://localhost/uiservices/';
$config['index_page']= '';
$config['environment']= 'development';
$config['calculate_performance']= false;
  
$config['controllers']= array(
    'index' => array(
        'class' => 'Index',
        'url' => '/'
    ),
    'uidefinition-component' => array(
        'class' => 'uidefinition/Component',
        'url' => 'uidefinition/component/(project)/(name)'
    ),
    'uidefinition-server' => array(
        'class' => 'uidefinition/Server',
        'url' => 'uidefinition/server/(api)'
    )
);
      
$config['filters']= array();
$config['filters_after_processing']= array();
    
$config['url-components']= 'enola-components';
$config['components']= array(
    'ui-component' => array(
        'class' => 'UI_Component'
    )
);
      
$config['libraries']= array(
    'twig' => array( 
        'path' => 'Twig-1.16.0/Twig'
    )
);
      
$config['dependency_injection']= array(
    'dependencyInjection'
);