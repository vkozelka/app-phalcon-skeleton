<?php
namespace App\Core;

use Exception;
use Phalcon\Cache;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di;
use Phalcon\Escaper;
use Phalcon\Events\Manager;
use Phalcon\Flash\Session;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Mvc\View;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Url;

class DiContainer extends Di {


    public function __construct($isCli = false)
    {
        parent::__construct();
        $this->registerGlobalServices();
        if ($isCli) {
            $this->registerGlobalServicesCli();
        } else {
            $this->registerGlobalServicesMvc();
        }
    }

    public function registerGlobalServices() {
        // Profiler
        $this->setShared('profiler', function() {
            return new Profiler();
        });
        // Config
        $this->setShared('config', function() {
            return new Config();
        });
        // Cache
        $this->setShared('cache', function() {
            $serializerFactory = new SerializerFactory();
            $adapterFactory = new AdapterFactory($serializerFactory);
            $adapter = $adapterFactory->newInstance('stream',[
                'storageDir' => CMS_DIR_VAR_CACHE,
                'lifetime' => CMS_ENV === 'development' ? 0 : 3600
            ]);
            return new Cache($adapter);
        });
        // Database
        $this->setShared('db', function() {
            $dbConfig = $this->get('config')->getConfigValues('database', true)[CMS_ENV];
            return new Mysql($dbConfig);
        });
        // Email
        $this->setShared('email', function() {
            return new Email();
        });
        // Escaper
        $this->setShared('escaper', function() {
            return new Escaper();
        });
        // Models Manager
        $this->setShared('modelsManager', function() {
            return new \Phalcon\Mvc\Model\Manager();
        });
        // Models Metadata
        $this->setShared('modelsMetadata', function() {
            return new \Phalcon\Mvc\Model\MetaData\Stream([
                'metaDataDir' => CMS_DIR_VAR_CACHE.DS
            ]);
        });
    }

    /**
     * @throws Exception
     */
    public function registerGlobalServicesMvc() {
        // Request
        $this->setShared('request', function() {
            return new Request();
        });
        // Logger
        $this->setShared('log', function() {
            $adapter = new Stream(CMS_DIR_VAR_LOG.DS.CMS_ENV.".log");
            $logger = new Logger(CMS_ENV, ['main' => $adapter]);
            return $logger;
        });
        // Session
        $this->setShared('session', function() {
            $adapter = new \Phalcon\Session\Adapter\Stream([
                'savePath' => CMS_DIR_VAR_SESSION
            ]);
            $manager = new \Phalcon\Session\Manager();
            $manager->setAdapter($adapter);
            return $manager;
        });
        // Response
        $this->setShared('response', function() {
            return new Response();
        });
        // Router
        $this->setShared('router', function() {
            return new Router();
        });
        // Url
        $this->setShared('url', function() {
            $systemConfig = $this->get('config')->getConfigValues('system', true);
            $uri = new Url($this->get('router'));
            $uri->setBaseUri($systemConfig[CMS_ENV]['base_url']);
            $uri->setBasePath($systemConfig[CMS_ENV]['base_path']);
            return $uri;
        });
        // Dispatcher
        $this->setShared('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setEventsManager($this->get('eventsManager'));
            return $dispatcher;
        });
        // View
        $this->setShared('view', function() {
            $view = new View();
            $view->disable();
            return $view;
        });
        // Flash Messages
        $this->setShared('flash', function() {
            return new Session();
        });
        // EventsManager
        $this->setShared('eventsManager', function() {
            $eventsManager = new Manager();
            $eventsManager->attach('dispatch', new \App\Core\Event\Listener\Dispatcher());
            return $eventsManager;
        });
    }

    /**
     * @throws Exception
     */
    public function registerGlobalServicesCli() {
        // Router
        $this->setShared('router', function() {
            return new \Phalcon\Cli\Router();
        });
        // Logger
        $this->setShared('log', function() {
            $adapter = new Stream("php://stdout");
            $logger = new Logger(CMS_ENV, ['main' => $adapter]);
            return $logger;
        });
        // Dispatcher
        $this->setShared('dispatcher', function() {
            $dispatcher = new \Phalcon\Cli\Dispatcher();
            $dispatcher->setEventsManager($this->get('eventsManager'));
            return $dispatcher;
        });
        // EventsManager
        $this->setShared('eventsManager', function() {
            $eventsManager = new Manager();
            $eventsManager->attach('dispatch', new \App\Core\Cli\Event\Listener\Dispatcher());
            return $eventsManager;
        });
    }

}