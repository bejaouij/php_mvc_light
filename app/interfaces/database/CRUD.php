<?php
	namespace App\Interfaces\Database;

	interface CRUD
	{
		public static function createMany(Array $models) : int;
		public static function readBy(string $field, string $value) : Array;
		public static function readAll() : Array;
		public static function updateMany(Array $models): int;
		public static function deleteMany(Array $models) : int;
		public static function deleteById(string $id) : int;

		public function create() : Model;
		public function readById($id) : Model;
		public function update() : Model;
		public function delete() : Model;
	}