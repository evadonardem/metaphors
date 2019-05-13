<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model {
	protected $table = 'purchase_order';

	protected $primaryKey = 'code';

	protected $fillable = array(
		'code',
		'purchase_order_date'
	);

	public function member() {
		return $this->belongsTo('Member');
	}

	public function products() {
		return $this->belongsToMany('Product', 'purchase_order_product', 'purchase_order_code', 'product_code')
		->withPivot('price', 'quantity')
		->withTimestamps();
	}
}
