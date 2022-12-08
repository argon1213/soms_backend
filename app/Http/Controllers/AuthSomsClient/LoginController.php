<?php

namespace App\Http\Controllers\AuthSomsClient;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;

use Auth;
use Hash;

use App\SomsClient;

class LoginController extends Controller
{
    /**
     * This trait has all the login throttling functionality.
     */
    use ThrottlesLogins;

    public function __construct()
    {
        // Allow Access to this page when guest in member
        // Except Logout Method
        $this->middleware('guest:somsclient')->except('logout');
    }
    //
    public function login()
    {
        return view('auth-somsclient.login');
    }

    public function loginSubmit(Request $request)
    {
        // Validate the form
        $request->validate([
          'email' => 'required|email',
          'password' => 'required|min:6'
          ]);

        //check if the user has too many login attempts.
        // if ($this->hasTooManyLoginAttempts($request)){
        //     //Fire the lockout event.
        //     $this->fireLockoutEvent($request);
        //     //redirect the user back after lockout.
        //     return $this->sendLockoutResponse($request);
        // }

        // Attempt to log the user in
        if(Auth::guard('somsclient')->attempt(['email' => $request->email, 'password'=>$request->password], $request->remember))
        {
          // if successful, then redirect to their intended Location
          return redirect()->intended(route('somsclient.dashboard'));
        }
        //keep track of login attempts from the user.
        // $this->incrementLoginAttempts($request);
        // if unsuccessful, the redirect back to the login form with form data
        return redirect()->back()->withInput($request->only('email','remember'));
    }

    public function logout()
    {
      Auth::guard('somsclient')->logout();
      return redirect(route('somsclient.login'));
    }

    /**
     * Username used in ThrottlesLogins trait
     *
     * @return string
     */
    public function username(){
        return 'email';
    }
}
