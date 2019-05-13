<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('member', function($table) {			
			$table->string('code', 10)->unique();
			$table->integer('person_id')->unsigned();
			$table->date('date_of_registration');							
			$table->primary('code');
			$table->timestamps();
			$table->foreign('person_id')->references('id')->on('person')->onDelete('cascade')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('member');
	}

}
