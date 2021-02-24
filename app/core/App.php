<?php
namespace App\Core;

use App\Core\App\AppTrait;
use Phalcon\Cache;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Escaper;
use Phalcon\Events\Manager;
use Phalcon\Flash\Session;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Logger;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;
use Phalcon\Url;

/**
 * Class App
 * @package App\Core
 *
 * @property-read Profiler $profiler
 * @property-read Request $request
 * @property-read Manager $eventsManager
 * @property-read \Phalcon\Session\Manager $session
 * @property-read Config $config
 * @property-read Cache $cache
 * @property-read Response $response
 * @property-read Router $router
 * @property-read Url $url
 * @property-read Dispatcher $dispatcher
 * @property-read View $view
 * @property-read Session $flash
 * @property-read Escaper $escaper
 * @property-read Email $email
 * @property-read Mysql $db
 * @property-read Logger $log
 */
final class App extends Application
{
    use AppTrait;

    protected static ?App $instance = null;

    private $started = false;

    public static function get(): App
    {
        if (null === self::$instance) {
            self::$instance = new self(new DiContainer());
        }
        return self::$instance;
    }

    public function run(): string
    {
        if (!$this->started) {
            $this->prepare();
            $this->router->match();
            $this->prepareModules();
            $this->started = true;
        }
        $cleanPath = str_replace($this->url->getBasePath(),"",$this->request->getServer("REQUEST_URI"));
        return $this->handle($cleanPath)->getContent();
    }

    public function outputProfiler(bool $returnOnly = false)
    {
        $events = [];
        foreach ($this->profiler->getTimers() as $timerName => $timerData) {
            $events[$timerName] = $timerData["duration"] . " ms";
        }
        if ($returnOnly === true) {
            return $events;
        } else {
            echo $this->view->render("__profiler", ["events" => $events]);
        }
    }

}