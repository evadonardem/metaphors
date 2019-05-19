<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderProduct extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_order_product', function($table) {
			$table->string('purchase_order_code', 18);
			$table->string('product_code', 15);
			$table->decimal('price', 7, 2);
			$table->integer('quantity');
			$table->timestamps();
			$table->primary(array('purchase_order_code', 'product_code'));
			$table->foreign('purchase_order_code')->references('code')->on('purchase_order')->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('product_code')->references('code')->on('product')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('purchase_order_product');
	}

}
