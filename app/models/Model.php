<?php
    namespace App\Models;

    use App\Interfaces\Database\CRUD;
    use App\Exceptions\InvalidDataAccessException;
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
        
        private static function getPrimaryKey() {
            return get_called_class()::$primaryKey;
        }

        private static function getDatabaseSchema() {
            return ConfigManager::getDatabaseConfig('schema');
        }

        private static function getFullTableName() {
            return self::getDatabaseSchema() . '.' . self::getMostLikelyTableName();
        }

        public static function createMany(array $models) : int {
            $affectedRowsCount = 0;

            foreach($models as $model) {
                $model->create();
                $affectedRowsCount++;
            }

            return $affectedRowsCount;
        }

        public static function readById(string $id) : Model {
            $queryBuilder = new QueryBuilder();

            $calledClass = get_called_class();
            $model = new $calledClass();

            $data = $queryBuilder->query('SELECT * FROM ' . self::getFullTableName() . ' WHERE ' . self::getPrimaryKey() . ' = ?', [$id]);

            if(count($data) > 0) {
                $data = array_shift($data);
            }

            $model->hydrate($data);

            return $model;
        }

        public static function readBy(string $field, string $value, string $operator = '=', bool $returnObject = true) : array {
            $calledClass = get_called_class();
            $queryBuilder = new QueryBuilder();

            $dataCollection = $queryBuilder->query('SELECT * FROM ' . self::getFullTableName() . ' WHERE ' . $field . ' ' . $operator . ' ?', [$value]);

            if($returnObject) {
                $models = array();

                foreach($dataCollection as $data) {
                    $model = new $calledClass();
                    $model = $model->hydrate($data);

                    array_push($models, $model);
                }

                return $models;
            } else {
                return $dataCollection;
            }
        }

        public static function readAll() : array {
            $queryBuilder = new QueryBuilder();

            return $queryBuilder->query('SELECT * FROM ' . self::getFullTableName());
        }

        public static function updateMany(array $models): int {
            $affectedRowsCount = 0;

            foreach($models as $model) {
                $model->update();
                $affectedRowsCount++;
            }

            return $affectedRowsCount;
        }

        public static function deleteMany(array $models) : int {
            $affectedRowsCount = 0;

            foreach($models as $model) {
                $affectedRowsCount += $model->delete(false) ? 1 : 0;
            }

            return $affectedRowsCount;
        }

        public static function deleteById(string $id) : bool {
            $affectedRowsCount = self::deleteBy(self::getPrimaryKey(), $id);

            return $affectedRowsCount == 1;
        }

        public static function deleteBy(string $field, string $value, string $operator = '=') : int {
            $queryBuilder = new QueryBuilder();

            $query = 'DELETE FROM ' . self::getFullTableName() . ' WHERE ' . $field . ' ' . $operator . ' ?';

            $data = $queryBuilder->query($query, [0 => $value]);

            return count($data);
        }

        public function create() : Model {
            $queryBuilder = new QueryBuilder();
            
            if(count($this->getData()) == 0) {
                throw new InvalidDataAccessException('No data to insert.');
            }

            $query = 'INSERT INTO ' . self::getFullTableName() . '(';

            $query .= implode(', ', array_keys($this->getData()));
            $query .= ') VALUES(';

            foreach(array_keys($this->getData()) as $column) {
                $query .= ':' . $column . ', ';
            }

            $query = substr_replace($query, ') RETURNING ', '-2');
            $query .= self::getPrimaryKey();

            $data = $queryBuilder->query($query, $this->getData());

            if(count($data) > 0) {
                $this->setData($this::$primaryKey, $data[0][$this::$primaryKey]);
            }

            #TODO REFRESH THE OBJECT TO RETRIEVE DEFAULT VALUE

            return $this;
        }

        public function update() : Model {
            $queryBuilder = new QueryBuilder();

            if(empty($this->getData(self::getPrimaryKey()))) {
                throw new InvalidDataAccessException('No value provided for primary key.');
            }

            if(count($this->getData()) == 1) {
                throw new InvalidDataAccessException('No data to update.');
            }

            $dataToUpdate = $this->getData();
            unset($dataToUpdate[self::getPrimaryKey()]);

            $query = 'UPDATE ' . self::getFullTableName() . ' SET ';

            foreach(array_keys($dataToUpdate) as $column) {
                $query .= $column . ' = :' . $column . ', ';
            }

            $query = substr_replace($query, ' WHERE ', -2);

            $query .= $this::$primaryKey . ' = :' . self::getPrimaryKey();

            $queryBuilder->query($query, $this->getData());

            return $this;
        }

        public function delete(bool $returnObject = true) {
            if(empty($this->getData(self::getPrimaryKey()))) {
                throw new InvalidDataAccessException('No value provided for primary key.');
            }

            $success = self::deleteById($this->getData(self::getPrimaryKey()));

            return $returnObject ? $this : $success;
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

        public function setData(string $field, ?string $value) : void {
            $this->_data[$field] = $value;
        }
    }