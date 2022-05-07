<?php

namespace Alimvc\PhpMvc\Db;

use Alimvc\PhpMvc\Application;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    { //$dsn = $_ENV['DB_DSN'], $use = $_ENV['DB_USER'], $password = $_ENV['DB_PASSWORD']
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationTable();//create table migration
        $applyMigrations = $this->getAppliedMigrations();// select all tabled migrated named ['m0001_initial', 'm0002_ss']

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR.'/Migrations');
        $toApplyMigrations = array_diff($files, $applyMigrations); // get the difference, from the file name and the selected from db, if not migrated then iterate through it

        foreach ($toApplyMigrations as $migration) {
            if($migration === '.' || $migration === ".."){
                continue;
            }

            require_once Application::$ROOT_DIR."/migrations/$migration";
            $className = pathinfo($migration, PATHINFO_FILENAME); //get the file name without extntion ex: m0001_i.php, it returns m000_i
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");

            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
             $this->log("All migrations are applied");
        }
    }

    public function createMigrationTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    migration VARCHAR(255),
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                                ) ENGINE=INNODB");
    }

    private function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare('SELECT migration FROM migrations');
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    private function saveMigrations(array $migrations)
    {
        $str = implode(',',array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }

    protected function log($message)
    {
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }
}