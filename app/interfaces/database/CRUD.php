<?php
	namespace App\Interfaces\Database;

	use App\Models\Model;

	interface CRUD
	{
		public static function createMany(Array $models) : int;
		public static function readBy(string $field, string $value) : array;
		public static function readAll() : array;
		public static function updateMany(array $models): int;
		public static function deleteMany(array $models) : int;
		public static function deleteById(string $id) : int;

		public function create() : Model;
		public function readById($id) : Model;
		public function update() : Model;
		public function delete() : Model;
	}