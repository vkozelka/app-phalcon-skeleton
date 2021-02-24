<?php
namespace App\Core\Mvc;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Model extends \Phalcon\Mvc\Model {

    protected $tableName;

    public function initialize() {
        $this->setSource($this->tableName);
    }

    protected function timestampable(bool $createdAt = true, bool $updatedAt = true, bool $deletedAt = false): void
    {
        $behaviorOptions = [];
        if ($createdAt === true) {
            $behaviorOptions["beforeCreate"] = [
                "field" => "created_at",
                "format" => "Y-m-d H:i:s"
            ];
        }
        if ($updatedAt === true) {
            $behaviorOptions["beforeUpdate"] = [
                "field" => "updated_at",
                "format" => "Y-m-d H:i:s"
            ];
        }
        if (count($behaviorOptions)) {
            $this->addBehavior(new Timestampable($behaviorOptions));
        }

        if ($deletedAt === true) {
            $this->addBehavior(new SoftDelete([
                "field" => "deleted_at",
                "value" => new RawValue("NOW()")
            ]));
        }
    }

}