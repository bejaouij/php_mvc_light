<?php
    namespace App\Helpers\Core\Query_Builder;

    use \PDO;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    class QueryBuilder
    {
        private $pdo;

        public function __construct() {
            $dbConfig = ConfigManager::getDatabaseConfig();

            $this->pdo = new PDO($dbConfig['driver'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'], $dbConfig['username'], $dbConfig['password']);
        }

        public function query($query, array $params = []) : array
        {
            if(count($params) == 0) {
                $statement = $this->pdo->query($query);
            } else {
                $statement = $this->pdo->prepare($query);

                $statement->execute($params);
            }

            return ($statement) ? $statement->fetchAll() : array();
        }
    }