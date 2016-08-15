<?php

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url;
use Phalcon\Config\Adapter\Ini as ConfigIni;

try {
    // Obtenemos el directorio raíz del proyecto
    define( 'APP_PATH', realpath( '..' ) . '/' );

    /**
    *  Leemos la configuración
    */
    $config = new ConfigIni( APP_PATH . 'app/config/config.ini' );
    if ( is_readable( APP_PATH . 'app/config/config.ini.dev' ) ) {
        $override = new ConfigIni( APP_PATH . 'app/config/config.ini.dev' );
        $config->merge( $override );
    }

    // Registro del autoloader, encargado de la cargar los controladores y más ...
    $loader = new Loader();
    $loader->registerDirs( array(
        APP_PATH . $config->application->controllersDir,
    ))->register();

    // Creamos un DI, inyector de dependencias, todo está desacoplado, por eso es tan rápido
    $di = new FactoryDefault();
 
    // Seteamos donde tendremos las vistas
    $di->set('view', function() use ( $config ) {
        $view = new View();
        $view->setViewsDir( APP_PATH . $config->application->viewsDir );
        return $view;
    });

    // Seteamos el baseurl, reemplaza /frases_celebres/ por tu proyecto,
    // Yo lo tengo en www/html/frases_celebres
    // Esto ayudara a acceder a los archivos por Url
    $di->set( 'url', function() use ( $config ) {
        $url = new Url();
        $url->setBaseUri( APP_PATH . $config->application->baseUri );
        return $url;
    });
 
    // Manejador de las peticiones al framework, recibe el contenedor con las dependencias
    $application = new Application( $di );

    // Se muesta el resultado
    echo $application->handle()->getContent();
 
} catch ( Exception $e ) {
    echo "PhalconException: ", $e->getMessage();
}

?>
