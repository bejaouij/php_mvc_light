<?php
    namespace App\Helpers\Core\Query_Builder;

    use \PDO;

    class QueryBuilder
    {
        private $pdo;

        public function __construct() {
            $dbConfig = require_once(__DIR__ . '/../../../config/database.php');

            $this->pdo = new PDO($dbConfig['driver'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'], $dbConfig['username'], $dbConfig['password']);
        }

        public function query($query) : array
        {
            $data = $this->pdo->query($query);

            return ($data) ? $data->fetchAll() : array();
        }
    }