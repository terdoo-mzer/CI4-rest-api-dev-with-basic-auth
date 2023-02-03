<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Category extends Migration
{
	public function up()
	{
		$this->forge->addField([
			"id" => [
				"type" => "INT",// INT, int
				"constraint" => 5,
				"unsigned" => true,
				"auto_increment" => true
			],
			"name" => [
				"type" => "VARCHAR",
				"constraint" => 150,
				"null" => false
			],
			"status" => [
				"type" => "ENUM",
				"constraint" => ['1', '0'],
				"default" => '1'
			]
		]);

		$this->forge->addPrimaryKey("id");
		$this->forge->createTable("categories");
	}

	public function down()
	{
		$this->forge->dropTable("categories");
	}
}
