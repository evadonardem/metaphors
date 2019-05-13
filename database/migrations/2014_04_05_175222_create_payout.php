<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayout extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payout', function($table) {
			$table->increments('id');
			$table->date('payout_from');
			$table->date('payout_to');
			$table->timestamps();
			$table->unique(array('payout_from', 'payout_to'));		
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payout');
	}

}
