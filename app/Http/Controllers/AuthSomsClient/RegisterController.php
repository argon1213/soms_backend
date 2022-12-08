<?php

namespace App\Http\Controllers\AuthSomsClient;

use Log;
use Auth;
use Mail;

use App\SomsClient;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SomsClientRegistrationRequest;

use App\Mail\SomsClientRegistrationNotification;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
  // protected $redirectTo = '/';

  public function __construct()
  {
      $this->middleware('guest:somsclient');
  }

  // protected function redirectTo()
  // {
  //     return route('somsclient-personal-center');
  // }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function register()
  {
    return view('/auth-somsclient/register');
  }

  public function registerSubmit(SomsClientRegistrationRequest $request)
  {
    $validated = $request->validated();
    // Log::info(json_encode($validated));
    // Create Record
    $somsClient = new SomsClient;
    $somsClient->name = $validated['name'];
    $somsClient->email = $validated['email'];
    $somsClient->password = bcrypt($validated['password']);
    //
    $somsClient->save();

    Auth::guard('somsclient')->login($somsClient);
    // Send Mail
    // try{
    //   Mail::to('alan.sklam@gmail.com')->cc( env('MAIL_TO_ADDRESS') )->send(new SomsclientRegistrationNotification($somsClient));
    //   //
    //   Auth::guard('somsclient')->login($somsClient);
    //   //
    //   return redirect()->back();
    // }
    // catch(\Exception $e){
    //   Log::error('SomsclientRegistrationNotification cannot send with email : alan.sklam@gmail.com somsClient id : '.$somsClient->id);
    // }
    //
    return redirect()->back();
  }
}
