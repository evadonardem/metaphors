<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Redirect;
use Response;

use App\User;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::user()->id)
            ->orderBy('email', 'ASC')
            ->with('roles')
            ->get();
        return Response::json($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('account.sign-up');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $email = $request->input('email');
        $username = $request->input('username');
        $password = $request->input('password');

        // generate activation code
        $code = str_random(60);

        $user = User::create(array(
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'code' => $code,
            'active' => 0
        ));

        $user->roles()->attach('ADMIN');

        if ($user) {

            // send email
            /* Mail::send('emails.auth.activate', array('link' => URL::route('account-activate', $code), 'username' => $username), function($message) use ($user) {
                $message->to($user->email, $user->username)->subject('Activate your account');
            }); */

            return Redirect::route('home')
                    ->with('global', 'Your account has been created! We have sent you an email to activate your account.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
    }

    public function signIn()
    {
        return view('account.sign-in');
    }

    public function authenticate(Request $request)
    {
        $user = array(
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'active' => true
        );

        if (Auth::attempt($user)) {
            return Redirect::route('home')
                ->with('flash_notice', 'You are successfully logged in.');
        }

        // authentication failure! lets go back to the login page
        return Redirect::route('account-sign-in')
            ->with('flash_error', 'Your username/password combination was incorrect')
            ->withInput();
    }

    public function signOut()
    {
        Auth::logout();
        return Redirect::route('home');
    }

    public function isUniqueEmail()
    {
        $validator = Validator::make(array('email' => $request->input('email')), array('email' => 'unique:users'));
        if ($validator->fails()) {
            echo 'EMAIL_NOT_UNIQUE';
        } else {
            echo 'EMAIL_UNIQUE';
        }
    }

    public function isUniqueUsername()
    {
        $validator = Validator::make(array('username' => $request->input('username')), array('username' => 'unique:users'));
        if ($validator->fails()) {
            echo 'USERNAME_NOT_UNIQUE';
        } else {
            echo 'USERNAME_UNIQUE';
        }
    }

    public function activate($code)
    {
        $user = User::where('code', '=', $code)->where('active', '=', 0);

        if ($user->count()) {
            $user = $user->first();

            // update user to active state
            $user->active = 1;
            $user->code = '';

            if ($user->save()) {
                return Redirect::route('home')
                        ->with('global', 'Activated! You can now sign-in!');
            }
        }

        return Redirect::route('home')
                ->with('global', 'We could not activate your account. Try again later.');
    }

    public function changePassword()
    {
    }
}
