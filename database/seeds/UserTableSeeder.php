<?php

use Illuminate\Database\Seeder;

use App\Models\User;

class UserTableSeeder extends Seeder {

	public function run() {
		DB::table('users')->delete();

		$users = [
			[
				'email' => 'evad_onardem@yahoo.com',
				'username' => 'dmedrano',
				'password' => Hash::make('dmedrano'),
				'remember_token' => '',
				'password_temp' => '',
				'code' => '',
				'active' => true
			],
			[
				'email' => 'evadonardem@gmail.com',
				'username' => 'vmedrano',
				'password' => Hash::make('vmedrano'),
				'remember_token' => '',
				'password_temp' => '',
				'code' => '',
				'active' => true
			]
		];

		foreach($users as $user) {
			User::create($user);
		}

		$user = User::where('email', 'evadonardem@gmail.com')->first();

		$user->roles()->attach('ADMIN');
		$user->roles()->attach('ENCODER');
		$user->roles()->attach('MEMBER');
	}

}
