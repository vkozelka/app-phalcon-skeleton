<?php
namespace App\Module\Core\Task;

use App\Core\Cli;
use App\Core\Cli\Task;

class DbTask extends Task {

    public function migrateAction() {
        $modules = Cli::get()->getModules();
        foreach ($modules as $moduleName => $module) {
            Cli::get()->log->info("Migrating module: ".$moduleName);
            $module["instance"]->migrate();
        }
    }

}