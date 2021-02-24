<?php
namespace App\Core\Database;

use App\Core\App;
use Phalcon\Db\ColumnInterface;
use Phalcon\Db\IndexInterface;
use Phalcon\Db\ReferenceInterface;

class Migration {
    
    public function createTable(string $table, array $definition): bool {
        App::get()->log->debug("-- Creating table: ".$table);
        return App::get()->db->createTable($table, $this->getSchema(), $definition);
    }

    public function dropTable(string $table, bool $ifExists = true): bool {
        App::get()->log->debug("-- Dropping table: ".$table);
        return App::get()->db->dropTable($table, $this->getSchema(), $ifExists);
    }

    public function addColumn(string $table, ColumnInterface $column): bool {
        App::get()->log->debug("-- Dropping column: ".$column->getName()." to table: ".$table);
        return App::get()->db->addColumn($table, $this->getSchema(), $column);
    }

    public function modifyColumn(string $table, ColumnInterface $column, ?ColumnInterface $currentColumn): bool {
        App::get()->log->debug("-- Updating column: ".$column->getName()." in table: ".$table);
        return App::get()->db->modifyColumn($table, $this->getSchema(), $column, $currentColumn);
    }

    public function dropColumn(string $table, string $columnName): bool {
        App::get()->log->debug("-- Dropping column: ".$columnName." from table: ".$table);
        return App::get()->db->dropColumn($table, $this->getSchema(), $columnName);
    }

    public function addIndex(string $table, IndexInterface $index): bool {
        App::get()->log->debug("-- Adding index: ".$index->getName()." to table: ".$table);
        return App::get()->db->addIndex($table, $this->getSchema(), $index);
    }

    public function dropIndex(string $table, string $indexName): bool {
        App::get()->log->debug("-- Dropping index: ".$indexName." from table: ".$table);
        return App::get()->db->dropIndex($table, $this->getSchema(), $indexName);
    }

    public function addForeignKey(string $table, ReferenceInterface $reference): bool {
        App::get()->log->debug("-- Adding foreign key: ".$reference->getName()." to table: ".$table);
        return App::get()->db->addForeignKey($table, $this->getSchema(), $reference);
    }

    public function dropForeignKey(string $table, string $referenceName): bool {
        App::get()->log->debug("-- Dropping foreign key: ".$referenceName." from table: ".$table);
        return App::get()->db->dropForeignKey($table, $this->getSchema(), $referenceName);
    }

    private function getSchema(): string {
        return App::get()->db->getDescriptor()["dbname"];
    }

}