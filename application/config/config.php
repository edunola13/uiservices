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
    ),
    
    'uiprint-component' => array(
        'class' => 'uiprint/Component',
        'url' => 'uiprint/component/(project)'
    ),
    'uiprint-theme' => array(
        'class' => 'uiprint/Theme',
        'url' => 'uiprint/theme/(project)'
    ),
    'uiprint-theme2' => array(
        'class' => 'uiprint/Theme',
        'url' => 'uiprint/theme/(project)/(name)'
    ),
    'uiprint-javascript' => array(
        'class' => 'uiprint/JavaScript',
        'url' => 'uiprint/javascript/(project)'
    ),
    'uiprint-javascript2' => array(
        'class' => 'uiprint/JavaScript',
        'url' => 'uiprint/javascript/(project)/(name)'
    ),
    
    'test' => array(
        'class' => 'Test',
        'url' => 'test/*'
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