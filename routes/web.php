<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authenticated group
Route::group(array('before' => 'auth'), function() {

	// Role is administrator
	Route::group(array('before' => 'administrator'), function() {
		// register member
		Route::get('/register', array('as' => 'register', 'uses' => 'MemberController@create'));
		Route::post('/register/generate-member-code', array('as' => 'register-generate-member-code', 'uses' => 'MemberController@generateMemberCode'));

		// members
		Route::get('/members', array('as' => 'members', 'uses' => 'MemberController@index'));		
		Route::get('/members/json', array('as' => 'members-json', 'uses' => 'MemberController@membersJSON'));
		Route::get('/members/printable', function() {
			return View::make('members.master-list-printable');
		});

		// member
		Route::group(array('prefix' => 'member'), function() {
			Route::get('/{memberCode}/downlines', array('as' => 'member-downlines', 'uses' => 'MemberController@downlines'));
			Route::get('/{memberCode}/downlines/json', array('as' => 'member-downlines-json', 'uses' => 'MemberController@downlinesJSON'));
			Route::get('/{memberCode}/downlines/printable', function($memberCode) {
				$member = Member::find($memberCode);
				return View::make('members.downlines-printable', array('member' => $member));
			});

			Route::get('/{memberCode}/purchase-order', array('as' => 'member-purchase-order', 'uses' => 'MemberController@purchaseOrder'));
			Route::get('/{memberCode}/purchase-orders', array('as' => 'member-purchase-orders', 'uses' => 'MemberController@purchaseOrders'));
			Route::get('/{memberCode}/purchase-orders/json', array('as' => 'member-purchase-orders-json', 'uses' => 'MemberController@purchaseOrdersJSON'));
			Route::get('/purchase-order/{purchaseOrderCode}/products/json', array('as' => 'member-purchase-order-products-json', 'uses' => 'MemberController@purchaseOrderProductsJSON'));

			Route::group(array('before' => 'csrf'), function() {
				Route::post('/{memberCode}/purchase-order', array('as' => 'member-purchase-order', 'uses' => 'MemberController@addPurchaseOrder'));
			});			
		});

		// payouts
		Route::group(array('prefix' => 'payouts'), function() {
			Route::get('/', array('as' => 'payouts', 'uses' => 'PayoutController@index'));
			Route::get('/json', array('as' => 'payouts-json', 'uses' => 'PayoutController@payoutsJSON'));
			Route::get('/{payoutFrom}/{payoutTo}/json', array('as' => 'payout-json', 'uses' => 'PayoutController@payoutJSON'));
			Route::group(array('before' => 'csrf'), function() {
				Route::post('/store', array('as' => 'payouts-store', 'uses' => 'PayoutController@store'));
			});			
			Route::get('/{payoutFrom}/{payoutTo}/printable', function($payoutFrom, $payoutTo) {
				$duration = array(
					'from' => new DateTime($payoutFrom),
					'to' => new DateTime($payoutTo)
				);
				return View::make('payouts.payout-printable', array('duration' => $duration));
			});
		});		

		// products 
		Route::get('/products', array('as' => 'products', 'uses' => 'ProductController@index'));
		Route::get('/products/json', array('as' => 'products-json', 'uses' => 'ProductController@productsJSON'));

		Route::group(array('before' => 'csrf'), function() {
			//register member
			Route::post('/register/unique/membercode', array('as' => 'register-unique-membercode', 'uses' => 'MemberController@isUniqueMemberCode'));
			Route::post('/register', array('as' => 'register', 'uses' => 'MemberController@store'));

			// products
			Route::post('/products/add', array('as' => 'products-add', 'uses' => 'ProductController@store'));
		});

		Route::get('/products/destroy/{id}', array('as' => 'products-destroy', 'uses' => 'ProductController@destroy')); 		

		// reports
		Route::group(array('prefix' => 'reports'), function() {
			Route::get('/', array('uses' => 'ReportsController@index'));
			Route::get('/dailysales', array('uses' => 'DailySalesController@index'));
			Route::get('/dailysales/{start}/to/{end}/json', array('uses' => 'DailySalesController@dailySalesJSON'));
			Route::get('/dailysales/{start}/to/{end}/print', array('uses' => 'DailySalesController@dailySalesPrint'));			
		});

	});

	// Role is account manager
	Route::group(array('before' => 'account-manager'), function() {
		Route::get('/users-management', function() {
			return View::make('users-management.master-list');
		});
		Route::get('/users/json', array('as' => 'users-json', 'uses' => 'UserController@index'));
	});
	
	// account: change password
	Route::get('/account/change-password', array('as' => 'account-change-password', 'uses' => 'UserController@changePassword'));

	// account: sign-out (get)
	Route::get('/account/sign-out', array('as' => 'account-sign-out', 'uses' => 'UserController@signOut')); 
});

// Unauthenticated group
Route::group(array('before' => 'guest'), function() {

	// CSRF protection
	Route::group(array('before' => 'csrf'), function() {
		// account: sign-up (post)
		Route::post('/account/sign-up', array('as' => 'account-sign-up',	'uses' => 'UserController@store'));		

		// account: sign-in (post)
		Route::post('/account/sign-in', array('as' => 'account-sign-in', 'uses' => 'UserController@authenticate'));		
	});	

	// account: sign-in (get)
	Route::get('/account/sign-in', array('as' => 'account-sign-in', 'uses' => 'UserController@signIn'));

	// account: sign-up (get)
	Route::get('/account/sign-up', array('as' => 'account-sign-up',	'uses' => 'UserController@create'));

	// account sign-up: check if email is unique (live validator)
	Route::post('/account/sign-up/check-email', array('as' => 'is-unique-email', 'uses' => 'UserController@isUniqueEmail'));

	// account sign-up: check if username is unique (live validator)
	Route::post('/account/sign-up/check-username', array('as' => 'is-unique-username', 'uses' => 'UserController@isUniqueUsername'));		
	
	// account: activate
	Route::get('/account/activate/{code}', array('as' => 'account-activate', 'uses' => 'UserController@activate')); 			
});

// home
Route::get('/', array('as' => 'home', 'uses' => 'HomeController@home'));

// test routes
Route::get('/test', function() {
	$user = User::find(1);
	$user->password = Hash::make('12345');
	$user->save();
});
