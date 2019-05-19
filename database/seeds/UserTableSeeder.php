<?php

use Illuminate\Database\Seeder;

use App\User;

class UserTableSeeder extends Seeder {

	public function run() {
		DB::table('users')->delete();

		$users = [
			[
				'email' => 'test1@abc.com',
				'username' => 'test1',
				'password' => Hash::make('test1'),
				'remember_token' => '',
				'password_temp' => '',
				'code' => '',
				'active' => true
			],
			[
				'email' => 'test2@abc.com',
				'username' => 'test2',
				'password' => Hash::make('test2'),
				'remember_token' => '',
				'password_temp' => '',
				'code' => '',
				'active' => true
			]
		];

		foreach($users as $user) {
			User::create($user);
		}

		$user = User::where('email', 'test1@abc.com')->first();

		$user->roles()->attach('ADMIN');
		$user->roles()->attach('ENCODER');
		$user->roles()->attach('MEMBER');
	}

}
