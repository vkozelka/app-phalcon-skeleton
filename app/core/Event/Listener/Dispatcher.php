<?php
namespace App\Core\Event\Listener;

use App\Core\Router;
use Phalcon\Dispatcher\DispatcherInterface;
use Phalcon\Events\Event;

class Dispatcher {

    public function beforeDispatchLoop(Event $event, DispatcherInterface $dispatcher) {
        /**
         * @var Router $router
         */
        $router = $event->getSource()->getDI()->get('router');
        $module = $router->getModuleName();
        $controller = $router->getControllerName();
        $action = $router->getActionName();
        $params = $router->getParams();
        $section = $router->getSection();

        $dispatcher->setSection($section);
        $dispatcher->setModuleName($module);
        $dispatcher->setControllerName($controller);
        $dispatcher->setActionName($action);
        $dispatcher->setParams($params);
        $dispatcher->setNamespaceName('App\\Module\\'.ucfirst(strtolower($module)).'\\Controller\\'.ucfirst(strtolower($section)));
        return $dispatcher;
    }

}