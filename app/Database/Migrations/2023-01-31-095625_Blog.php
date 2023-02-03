<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Blog extends Migration
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
			"category_id" => [
				"type" => "INT",// INT, int
				"constraint" => 5,
				"unsigned" => true
			],
			"title" => [
				"type" => "VARCHAR",
				"constraint" => 150,
				"null" => false
			],
			"content" => [
				"type" => "TEXT",
				"null" => true
			]
		]);

		$this->forge->addPrimaryKey("id");

		$this->forge->createTable("blogs");
	}

	public function down()
	{
		$this->forge->dropTable("blogs");
	}
}
