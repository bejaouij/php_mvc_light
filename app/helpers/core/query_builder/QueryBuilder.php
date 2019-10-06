<?php
    namespace App\Helpers\Core\Query_Builder;

    use \PDO;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    final class QueryBuilder
    {
        private $pdo;

        public function __construct() {
            $dbConfig = ConfigManager::getDatabaseConfig();

            $this->pdo = new PDO($dbConfig['driver'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'], $dbConfig['username'], $dbConfig['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        public function query($query, array $params = []) : array
        {
            try {
                if(count($params) == 0) {
                    $statement = $this->pdo->query($query);
                } else {
                    $statement = $this->pdo->prepare($query);

                    $statement->execute($params);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }

            return ($statement) ? $statement->fetchAll() : array();
        }
    }