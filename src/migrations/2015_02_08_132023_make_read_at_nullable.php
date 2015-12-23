<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeReadAtNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `'.DB::getTablePrefix().'notification_user` MODIFY `read_at` TIMESTAMP NULL DEFAULT NULL;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `'.DB::getTablePrefix().'notification_user` MODIFY `read_at` TIMESTAMP NOT NULL DEFAULT  \'0000-00-00 00:00:00\';');
	}

}
