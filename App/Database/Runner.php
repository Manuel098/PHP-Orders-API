<?php
namespace App\Database;
use App\Database\Schemas\SchemaSQL as Schema;

class Runner
{
    private Schema $schema;
    /**
     * Init SQL schema
     */
    public function __construct( $schema ) {
        $this->schema = $schema;
    }

    /**
     * GET all migrations files on migrations folder
     * return list of migrations: array
     */
    private function getMigrationFiles(): array
    {
        $path = __DIR__ . '/Migrations/*.php';
        $migrationFiles = glob($path);

        sort($migrationFiles);
        return $migrationFiles;
    }

    /**
     * Eject all migrations provider by getMigrationsFiles
     * return void
     */
    public function run(): void
    {
        foreach ($this->getMigrationFiles() as $file) {
            $migration = require $file;
            $migration->up($this->schema);
        }
    }
    
    /**
     * Eject all migrations provider by getMigrationsFiles in reverse mode
     * return void
     */
    public function rollback(): void
    {
        foreach (array_reverse($this->getMigrationFiles()) as $file) {
            $migration = require $file;
            $migration->down($this->schema);
        }
    }
}