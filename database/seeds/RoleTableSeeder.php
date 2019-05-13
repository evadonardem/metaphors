<?php

use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleTableSeeder extends Seeder {

	public function run() {
		DB::table('roles')->delete();

		$roles = [
			[
				'id' => 'ADMIN',
				'description' => ''
			],
			[
				'id' => 'ENCODER',
				'description' => ''
			],
			[
				'id' => 'MEMBER',
				'description' => ''
			]
		];

		foreach($roles as $role) {
			Role::create($role);
		}

	}

}
