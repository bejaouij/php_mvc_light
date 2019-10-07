<?php
	namespace App\Interfaces\Database;

	use App\Models\Model;

	interface CRUD
	{
		public static function createMany(Array $models) : int;
        public static function readById(string $id) : Model;
		public static function readBy(string $field, string $value, string $operator = '=') : array;
		public static function readAll() : array;
		public static function updateMany(array $models): int;
		public static function deleteMany(array $models) : int;
		public static function deleteById(string $id) : bool;
		public static function deleteBy(string $field, string $value, string $operator = '=') : int;

		public function create() : Model;
		public function update() : Model;
		public function delete(bool $returnObject = true);
	}