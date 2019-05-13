<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrder extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_order', function($table) {
			$table->string('code', 18);
			$table->date('purchase_order_date');
			$table->string('member_code', 10);
			$table->timestamps();
			$table->primary('code');
			$table->foreign('member_code')->references('code')->on('member')->onDelete('cascade')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('purchase_order');
	}

}
