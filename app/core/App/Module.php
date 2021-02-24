<?php

namespace App\Core\App;

use App\Core\Cli as App;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

    protected $moduleName;

    protected $migrated = false;

    public function registerAutoloaders(\Phalcon\Di\DiInterface $container = null)
    {
        // TODO: Implement registerAutoloaders() method.
    }

    public function registerServices(\Phalcon\Di\DiInterface $container)
    {
        // TODO: Implement registerServices() method.
    }

    private function getModuleVersionConfig(): string
    {
        return App::get()->config->getConfigValues('modules', true)[$this->moduleName]["version"];
    }

    private function getModuleVersionDatabase(): string
    {
        $result = "0.0.0";
        if (App::get()->db->tableExists("core_module")) {
            $result = $this->getInstalledModuleVersion($result);
        }
        return $result;
    }

    public function migrate() {
        if (!$this->migrated) {
            App::get()->log->debug("Intializing module: ".$this->moduleName);
            $cfgVersion = $this->getModuleVersionConfig();
            $dbVersion = $this->getModuleVersionDatabase();
            if ($cfgVersion != $dbVersion) {
                $migrationFiles = $this->getMigrationFiles($cfgVersion, $dbVersion);

                if (count($migrationFiles)) {
                    App::get()->log->debug("- Found ".count($migrationFiles)." migration files");
                    $this->processMigrations($migrationFiles);
                }
            }
            $this->migrated = true;
        }
    }

    private function getMigrationFiles($fromVersion = "0.0.0", $toVersion = "999.99.99")
    {
        if (version_compare($fromVersion, $toVersion) > 0) {
            $tmpVersion = $toVersion;
            $toVersion = $fromVersion;
            $fromVersion = $tmpVersion;
        }
        $dir = CMS_DIR_APP_MODULE . DS . ucfirst($this->moduleName) . DS . "migrations";
        if (!file_exists($dir) || !is_dir($dir)) {
            return [];
        }

        $migrationsDir = new \DirectoryIterator(CMS_DIR_APP_MODULE . DS . ucfirst($this->moduleName) . DS . "migrations");
        $migrationsFiles = [];
        foreach ($migrationsDir as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filename = $file->getFilename();
                $version = str_replace(".php", "", $filename);

                if (version_compare($fromVersion, $version, '<=') && version_compare($toVersion, $version, '>=')) {
                    $migrationsFiles[] = [
                        "version" => $version,
                        'path' => $file->getPathname(),
                        "class" => 'Migration' . ucfirst($this->moduleName) . str_replace(".", "", $version)
                    ];
                }
            }
        }

        uasort($migrationsFiles, function ($a, $b) {
            return version_compare($a["version"], $b["version"]);
        });
        return $migrationsFiles;
    }

    private function processMigrations(array $migrationFiles): bool
    {
        foreach ($migrationFiles as $migrationFile) {
            $this->processMigration(
                $migrationFile["version"],
                $migrationFile["path"],
                $migrationFile["class"]
            );
        }
        return true;
    }

    private function processMigration(string $version, string $filename, string $className)
    {
        App::get()->log->debug("- Processing migration: \nClass: ".$className."\nVersion: ".$version);
        require_once $filename;
        $class = new $className;
        $class->up();
        App::get()->log->debug("- Processed migration: \nClass: ".$className."\nVersion: ".$version);

        $module = \App\Module\Core\Model\Module::findFirst([
            "conditions" => "[module] = :m:",
            "bind" => ["m" => $this->moduleName]
        ]);
        if (!$module) {
            $module = new \App\Module\Core\Model\Module([
                "module" => $this->moduleName
            ]);
        }
        $module->version = $version;
        if (!$module->save()) {
            throw new \Exception("Cannot update module version in db for module ".$this->moduleName." v".$version);
        }
    }

    private function getInstalledModuleVersion(string $defaultVersion) {
        $module = \App\Module\Core\Model\Module::findFirst([
            "conditions" => "[module] = :m:",
            "bind" => [
                "m" => $this->moduleName
            ]
        ]);
        if ($module) {
            return $module->version;
        }
        return $defaultVersion;
    }

}