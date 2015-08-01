<?php

use Phalcon\DI;
use Phalcon\Loader;
use Phalcon\Config;
use Phalcon\Dispatcher;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Mvc\View as MvcView;
use Phalcon\Mvc\Router as MvcRouter;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as MvcDispatchException;
use Phalcon\Mvc\Model\Manager as MvcModelsManager;
use Phalcon\Mvc\Model\Metadata\Memory as MvcModelMetadataMemory;

class Application extends MvcApplication
{

    protected function _registerAutoloaders()
    {
        $loader = new Loader();
        
        $loader->registerDirs([
            ROOT_DIR . '/app/Controllers/',
            ROOT_DIR . '/app/Models/',
            ROOT_DIR . '/app/Views/',
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
            $router = new MvcRouter();
            $router->setUriSource(MvcRouter::URI_SOURCE_SERVER_REQUEST_URI);
            foreach ((include ROOT_DIR . "/config/router.php") as $key=>$value) {
                $router->add($key, $value);
            };
            return $router;
        });

        //Registering a dispatcher
        $di->set('dispatcher', function(){
            //Create an EventsManager
            $eventsManager = new EventsManager();
            //Attach a listener
            $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {
                //Handle controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward([
                                'controller' => 'error',
                                'action'     => 'index',
                                'params'     => ['message' => $exception->getMessage()],
                            ]);
                            return false;
                    }
                }
            });

            $dispatcher = new MvcDispatcher();
            $dispatcher->setDefaultNamespace('App\Controllers\\');
            $dispatcher->setEventsManager($eventsManager);
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
        $di->set('view', function(){
            $view = new MvcView();
            $view->setViewsDir(ROOT_DIR . '/app/Views/');
            return $view;
        });

        /*$di->set('view', function(){
            $view = new MvcView();
            $view->setViewsDir(ROOT_DIR . '/app/Views/');
            $view->registerEngines([
                '.html' => function($view, $di) {
                    $smarty = new \Phalbee\Base\View\Engine\Smarty($view, $di);
                    $smarty->setOptions([
                        'left_delimiter' => '<{',
                        'right_delimiter' => '}>',
                        'template_dir'      => ROOT_DIR . '/app/Views',
                        'compile_dir'       => ROOT_DIR . '/runtime/Smarty/compile',
                        'cache_dir'         => ROOT_DIR . '/runtime/Smarty/cache',
                        'error_reporting'   => error_reporting() ^ E_NOTICE,
                        'escape_html'       => true,
                        'force_compile'     => false,
                        'compile_check'     => true,
                        'caching'           => false,
                        'debugging'         => true,
                    ]);
                    return $smarty;
                },
            ]);
            return $view;
        });*/

        $di->set('smarty', function(){
            $smarty = new \Smarty();
            $options = [
                'left_delimiter' => '<{',
                'right_delimiter' => '}>',
                'template_dir'      => ROOT_DIR . '/app/Views',
                'compile_dir'       => ROOT_DIR . '/runtime/Smarty/compile',
                'cache_dir'         => ROOT_DIR . '/runtime/Smarty/cache',
                'error_reporting'   => error_reporting() ^ E_NOTICE,
                'escape_html'       => true,
                'force_compile'     => false,
                'compile_check'     => true,
                'caching'           => false,
                'debugging'         => true,
            ];
            foreach ($options as $k => $v) {
                $smarty->$k = $v;
            };
            return $smarty;
        });

        $di->set('db', function(){
            $db = include(ROOT_DIR . "/config/db.php");
            return new Database($db);
        });

        //Registering the Models-Metadata
        $di->set('modelsMetadata', function(){
            return new MvcModelMetadataMemory();
        });

        //Registering the Models Manager
        $di->set('modelsManager', function(){
            return new MvcModelsManager();
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
