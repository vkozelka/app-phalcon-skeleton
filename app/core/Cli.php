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
use Phalcon\Cli\Console as Application;
use Phalcon\Url;

/**
 * Class App
 * @package App\Core
 *
 * @property-read Profiler $profiler
 * @property-read Request $request
 * @property-read Manager $eventsManager
 * @property-read Config $config
 * @property-read Cache $cache
 * @property-read \Phalcon\Cli\Dispatcher $dispatcher
 * @property-read Escaper $escaper
 * @property-read Email $email
 * @property-read Mysql $db
 * @property-read Logger $log
 */
final class Cli extends Application
{
    use AppTrait;

    protected static ?Cli $instance = null;

    public static function get(): Cli
    {
        if (null === self::$instance) {
            self::$instance = new self(new DiContainer(true));
        }
        return self::$instance;
    }

    public function run(array $args): void
    {
        $arguments = [];
        foreach ($args as $k => $arg) {
            if ($k === 1) {
                $arguments['module'] = $arg;
            } elseif ($k === 2) {
                $arguments['task'] = $arg;
            } elseif ($k === 3) {
                $arguments['action'] = $arg;
            } elseif ($k >= 4) {
                $arguments['params'][] = $arg;
            }
        }

        $this->prepare();
        $this->prepareModules();
        $this->handle($arguments);
    }

}