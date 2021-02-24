<?php
namespace App\Module\Core\Model;

use App\Core\Mvc\Model;

class Module extends Model {

    protected $tableName = "core_module";

    public function initialize()
    {
        parent::initialize();
        $this->timestampable(true, true, false);
    }

}