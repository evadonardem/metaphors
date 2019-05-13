<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

	protected $table = 'product';

	protected $primaryKey = 'code';

	protected $fillable = array(
		'code',
		'title',
		'description',
		'price'
	);
}
