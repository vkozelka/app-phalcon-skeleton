<?php
namespace App\Core;

use App\Core\Config\Exception\ConfigFileNotFoundException;
use Phalcon\Config\ConfigFactory;

final class Config {

    private ConfigFactory $factory;

    private array $configs = [];

    public function __construct()
    {
        $this->factory = new ConfigFactory();
    }

    public function getConfigFile(string $filename) {
        if (!file_exists(CMS_DIR_CONFIG.DS.$filename)) {
            return false;
        }
        return CMS_DIR_CONFIG.DS.$filename;
    }

    public function getConfigValues($name, bool $asArray = true) {
        App::get()->profiler->start("App::Config::".$name);
        if (!isset($this->configs[$name])) {
            $config = App::get()->cache->get('config_'.$name);
            if (!$config) {
                $foundConfig = $this->getConfigFile($name.".php");
                if (!$foundConfig) {
                    throw new ConfigFileNotFoundException();
                }
                $this->configs[$name] = $this->factory->newInstance('php', $foundConfig);
                App::get()->cache->set('config_'.$name, $this->configs[$name]);
            } else {
                $this->configs[$name] = $config;
            }
        }

        App::get()->profiler->stop("App::Config::".$name);
        if ($asArray) {
            return $this->configs[$name]->toArray();
        }
        return $this->configs[$name];
    }

}