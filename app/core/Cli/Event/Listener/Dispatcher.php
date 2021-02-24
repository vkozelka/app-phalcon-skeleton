<?php
namespace App\Core\Cli\Event\Listener;

use Phalcon\Events\Event;
use Phalcon\Exception;

class Dispatcher {

    public function beforeDispatchLoop(Event $event, \Phalcon\Cli\Dispatcher $dispatcher) {
        $dispatcher->setNamespaceName('App\\Module\\'.ucfirst(strtolower($dispatcher->getModuleName())).'\\Task\\');
        return $dispatcher;
    }

}