<?php

use Phalcon\DI;
use Phalcon\Loader;
use Phalcon\Config;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Mvc\Model\Metadata\Memory as MemoryMetaData;

class Application extends MvcApplication
{

    protected function _registerAutoloaders()
    {
        $loader = new Loader();
        
        $loader->registerNamespaces([
            /*'App'                  => ROOT_DIR . '/app/',
            'App\Controller'       => ROOT_DIR . '/app/controllers/',
            'App\Model'            => ROOT_DIR . '/app/models/',
            'App\View'             => ROOT_DIR . '/app/views/',*/
        ]);
        
        $loader->register();
    }

    /**
     * This methods registers the services to be used by the application
     */
    protected function _registerServices()
    {
        $di = new DI();

        //Registering a router
        $di->set('router', function(){
            $router = new Router();
            foreach ((include ROOT_DIR . "/app/Configs/router.php") as $key=>$value) {
                $router->add($key, $value);
            }
            $router->notFound([
                'controller'=>'error',
                'action'=>'error404',
            ]);
            return $router;
        });

        //Registering a dispatcher
        $di->set('dispatcher', function(){
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('App\Controllers\\');
            return $dispatcher;
        });

        //Registering a Http\Response
        $di->set('response', function(){
            return new Response();
        });

        //Registering a Http\Request
        $di->set('request', function(){
            return new Request();
        });

        //Registering the view component
        /*$di->set('view', function(){
            $view = new View();
            $view->setViewsDir(ROOT_DIR . '/app/Views/');
            return $view;
        });*/
        $di->set('view', function(){
            $view = new View();
            $view->setViewsDir(ROOT_DIR . '/app/Views/');
            $view->registerEngines([
                '.html' => function($view, $di) {
                    $smarty = new \Phalbee\Base\View\Engine\Smarty($view, $di);
                    $smarty->setOptions([
                        'template_dir'      => ROOT_DIR . '/app/Views',
                        'compile_dir'       => ROOT_DIR . '/runtime/Smarty/compile',
                        'cache_dir'         => ROOT_DIR . '/runtime/Smarty/cache',
                        'error_reporting'   => error_reporting() ^ E_NOTICE,
                        'escape_html'       => true,
                        '_file_perms'       => 0666,
                        '_dir_perms'        => 0777,
                        'force_compile'     => false,
                        'compile_check'     => true,
                        'caching'           => false,
                        'debugging'         => true,
                    ]);
                    return $smarty;
                },
            ]);
            return $view;
        });

        $di->set('db', function(){
            $db = include(ROOT_DIR . "/app/configs/db.php");
            return new Database($db);
        });

        //Registering the Models-Metadata
        $di->set('modelsMetadata', function(){
            return new MemoryMetaData();
        });

        //Registering the Models Manager
        $di->set('modelsManager', function(){
            return new ModelsManager();
        });

        $this->setDI($di);
    }

    public function run()
    {
        $this->_registerServices();
        $this->_registerAutoloaders();

        echo $this->handle()->getContent();
    }
}
