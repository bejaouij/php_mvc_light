<?php
    namespace App\Models;

    use App\Interfaces\Database\CRUD;
    use App\Helpers\Core\Query_Builder\QueryBuilder;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    abstract class Model implements CRUD
    {
        static protected $tableName;
        static protected $primaryKey = 'id';

        # TODO IMPLEMENT A HIDDEN FIELDS FEATURE

        private $_data = array();

        private static function getMostLikelyTableName() {
            return get_called_class()::$tableName ? get_called_class()::$tableName : (strtolower((new \ReflectionClass(get_called_class()))->getShortName()));
        }

        private static function getDatabaseSchema() {
            return ConfigManager::getDatabaseConfig('schema');
        }

        public static function createMany(Array $models) : int {
            throw new \Exception('Not implemented.');
        }

        public static function readById(string $id) : Model {
            $queryBuilder = new QueryBuilder();

            $calledClass = get_called_class();
            $model = new $calledClass();

            $data = $queryBuilder->query('SELECT * FROM ' . self::getDatabaseSchema() . '.' . self::getMostLikelyTableName() . ' WHERE ' . get_called_class()::$primaryKey . ' = ?', [$id]);

            if(count($data) > 0) {
                $data = array_shift($data);
            }

            $model->hydrate($data);

            return $model;
        }

        public static function readBy(string $field, string $value) : array {
            $queryBuilder = new QueryBuilder();

            return $queryBuilder->query('SELECT * FROM ' . self::getDatabaseSchema() . '.' . self::getMostLikelyTableName() . ' WHERE ' . $field . ' = ?', [$value]);
        }

        public static function readAll() : array {
            $queryBuilder = new QueryBuilder();

            return $queryBuilder->query('SELECT * FROM ' . self::getDatabaseSchema() . '.' . self::getMostLikelyTableName());
        }

        public static function updateMany(array $models): int {
            throw new \Exception('Not implemented.');
        }

        public static function deleteMany(array $models) : int {
            throw new \Exception('Not implemented.');
        }

        public static function deleteById(string $id) : bool {
            throw new \Exception('Not implemented.');
        }

        public static function deleteBy(string $field, string $value) : int {
            throw new \Exception('Not implemented.');
        }

        public function create() : Model {
            $queryBuilder = new QueryBuilder();

            $query = 'INSERT INTO ' . $this::getDatabaseSchema() . '.' . $this::getMostLikelyTableName() . '(';

            $query .= implode(', ', array_keys($this->getData()));
            $query .= ') VALUES(';

            foreach(array_keys($this->getData()) as $column) {
                $query .= ':' . $column . ', ';
            }

            $query = substr_replace($query, ') RETURNING ', '-2');
            $query .= get_called_class()::$primaryKey;

            $data = $queryBuilder->query($query, $this->getData());

            if(count($data) > 0) {
                $this->setData($this::$primaryKey, $data[0][$this::$primaryKey]);
            }

            return $this;
        }

        public function update() : Model {
            throw new \Exception('Not implemented.');
        }

        public function delete() : Model {
            throw new \Exception('Not implemented.');
        }

        private function hydrate(array $data) : Model {
            foreach($data as $column => $value) {
                $this->setData($column, $value);
            }

            return $this;
        }

        public function getData(string $field = NULL) {
            return !is_null($field) ? $this->_data[$field] : $this->_data;
        }

        public function setData(string $field, string $value) : void {
            $this->_data[$field] = $value;
        }
    }