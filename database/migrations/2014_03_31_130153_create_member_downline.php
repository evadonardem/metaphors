<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDownline extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('member_downline', function($table) {
			$table->string('member_code', 10);
			$table->string('sponsor_code', 10);			
			$table->timestamps();		
			$table->primary(array('member_code', 'sponsor_code'));
			$table->foreign('member_code')->references('code')->on('member')->onDelete('cascade')->onUpdate('cascade');	
			$table->foreign('sponsor_code')->references('code')->on('member')->onDelete('cascade')->onUpdate('cascade');	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('member_downline');
	}

}
