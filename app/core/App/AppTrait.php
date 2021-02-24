<?php
namespace App\Core\App;

use App\Core\Filesystem;

trait AppTrait {

    private function prepare()
    {
        Filesystem::checkDir(CMS_DIR_VAR, true);
        Filesystem::checkDir(CMS_DIR_VAR_CACHE, true);
        Filesystem::checkDir(CMS_DIR_VAR_LOG, true);
        Filesystem::checkDir(CMS_DIR_VAR_SESSION, true);
    }

    public function prepareModules() {
        $modules = $this->config->getConfigValues('modules', true);
        $modulesDefinition = [];
        uasort($modules, function($a,$b) {
            return $a["priority"] > $b["priority"];
        });

        foreach ($modules as $name => $module) {
            $className = 'App\\Module\\'.$module['name'].'\\Module';
            $path = CMS_DIR_APP_MODULE.DS.$module['name'].DS.'Module.php';
            $instance = new $className();

            $modulesDefinition[$name] = [
                "className" => $className,
                "path" => $path,
                "priority" => $module["priority"],
                "instance" => $instance
            ];
        }
        $this->registerModules($modulesDefinition);
    }

    public function __get($name)
    {
        return $this->getDI()->getShared($name);
    }


}