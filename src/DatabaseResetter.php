<?php

namespace Src;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\MySqlBuilder;

class DatabaseResetter
{
    private MySqlBuilder $schema;

    public function run()
    {
        $this->setSchema('');
        $this->dropDatabaseIfExists($_ENV['DB_DATABASE']);
        $this->createDatabase($_ENV['DB_DATABASE']);
        $this->setSchema($_ENV['DB_DATABASE']);

        $this->createTables();
    }

    private function createTables(): void
    {
        $this->createTableReferences();
        $this->createTableBrands();
        $this->createTableTypes();
    }

    private function createTableBrands(): void
    {
        $this->schema->create('brands', function (Blueprint $table) {
            $table->integer('id')->primary();    
            $table->string('name', 50)->unique();
        });

        echo "Created table brands!";
    }

    private function createTableReferences(): void
    {
        $this->schema->create('references', function (Blueprint $table) {
            $table->integer('id')->primary();    
            $table->string('reference', 20)->unique();
        });

        echo "Created table references!";
    }

    private function createTableTypes(): void
    {
        $this->schema->create('types', function (Blueprint $table) {
            $table->integer('id')->primary();    
            $table->string('type', 20)->unique();
        });

        echo "Created table types!";
    }

    private function createDatabase(string $dataBaseName): void
    {
        $this->schema->getConnection()->statement("CREATE DATABASE {$dataBaseName} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
        echo "Created Database {$dataBaseName} <br>";
    }

    private function dropDatabaseIfExists(string $dataBaseName): void
    {
        $this->schema->getConnection()->statement("DROP DATABASE IF EXISTS {$dataBaseName}");
        echo "Dropped Database {$dataBaseName} if exists <br>";
    }
    
    private function setSchema(string $dataBaseName = ''): void
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => $_ENV['DB_DRIVER'],
            'host'      => $_ENV['DB_HOST'],
            'port'      => $_ENV['DB_PORT'],
            'database'  => $dataBaseName,
            'username'  => $_ENV['DB_USERNAME'],
            'password'  => $_ENV['DB_PASSWORD'],
        ]);
        
        $connection = $capsule->getConnection();

        $this->schema = $connection->getSchemaBuilder();
        echo "Setted schema {$dataBaseName} <br>";
    }
}