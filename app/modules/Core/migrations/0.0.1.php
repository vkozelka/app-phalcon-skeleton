<?php
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;

final class MigrationCore001 extends \App\Core\Database\Migration {

    public function up() {
        $this->createTable("core_module",[
            "columns" => [
                new Column('module', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 100,
                    'notNull' => true,
                    'primary' => true
                ]),
                new Column('version',[
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 20,
                    'notNull' => true
                ]),
                new Column('created_at',[
                    'type' => Column::TYPE_DATETIME,
                    'notNull' => false
                ]),
                new Column('updated_at',[
                    'type' => Column::TYPE_DATETIME,
                    'notNull' => false
                ])
            ]
        ]);
    }

}