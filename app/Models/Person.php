<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model {
	protected $table = 'person';

	protected $fillable = array(
		'discr',
		'firstName',
		'middleName',
		'lastName',
		'gender'
	);

	public function member() {
		return $this->hasOne('Member');
	}
}
