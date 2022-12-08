<?php

namespace App\Http\Controllers;

use App\OtpMsg;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\SomsCoupon;
use App\SomsClient;
use App\SomsDatetime;
use App\SomsLocation;
use App\SomsUniversity;
use App\SomsOrder;
use App\SomsOrderPayment;

use App\SomsItem;
use App\SomsItemPrice;

use App\SomsStoragePeriodItem;

use App\SomsPromotion;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Log;
use Auth;

class AjaxController extends Controller
{
    public function clientLogin(Request $request)
    {
        $username = $request->get('email');
        $password = $request->get('password');

        $credentials = $request->only('email', 'password');

        if (Auth::guard('somsclient')->attempt($credentials)) {
            // login success
            return Auth::guard('somsclient')->user();
        }
        // Login fail
        return response()->json(false);
    }

    public function getStoragePeriodItem(Request $request)
    {
        $somsStoragePeriodItems = SomsStoragePeriodItem::where('storage_period_id', $request->get('storage_period_id'))->whereNotNull('price')->pluck('price', 'item_id')->all();

        return ($somsStoragePeriodItems) ? $somsStoragePeriodItems : array();
    }

    public function universityOptions(Request $request)
    {
        $result = SomsUniversity::get()->pluck('display_name', 'id');

        return response()->json($result);
    }

    public function getOrderItemPrice(Request $request)
    {
        $itemId = $request->get('item_id');
        $universityId = $request->get('university_id');
        //
        $somsItemPrice = SomsItemPrice::where('university_id', $universityId)->where('item_id', $itemId)->first();
        $somsItem = SomsItem::find($itemId);

        if ($somsItemPrice) {
            return response()->json($somsItemPrice->price);
        } else if ($somsItem) {
            return response()->json($somsItem->price);
        } else {
            return response()->json(-1);
        }
    }

    public function getOrderPaymentStatus(Request $request)
    {
        // $orderCode = $request->get('code');
        // $order = SomsOrder::where('code', $orderCode)->first();
        // return response()->json($order->payment_status_id);
        $paymentCode = $request->get('code');
        $payment = SomsOrderPayment::where('code', $paymentCode)->first();
        return response()->json($payment->payment_status_id);
    }

    public function universityInfo(Request $request)
    {
        $university = $request->get('university');
        $somsLocations = SomsLocation::where('university_id', $university)->get();
        $somsDatetimes = SomsDatetime::where('university_id', $university)->get();

        $result['universityLocation'] = $somsLocations;
        $result['universityDatetime'] = $somsDatetimes;

        return response()->json($result);
    }

    public function universityLocation(Request $request)
    {
        $university = $request->get('university');
        $somsLocations = SomsLocation::where('university_id', $university)->get();

        return response()->json($somsLocations);
    }

    public function universityDatetime(Request $request)
    {
        $university = $request->get('university');
        $somsDatetimes = SomsDatetime::where('university_id', $university)->get();

        return response()->json($somsDatetimes);
    }

    public function couponCodeValidate(Request $request)
    {
        $university = 0;
        $universityPrices = array();

        $couponCode = $request->get('couponCode');
        // Log::debug($couponCode);
        $somsCoupon = SomsCoupon::where('coupon', $couponCode)->first();
        // Log::debug(json_encode($somsCoupon));
        if ($somsCoupon) {
            $university = $somsCoupon->university_id;
            $universityPrices = SomsItemPrice::where('university_id', $somsCoupon->university_id)->get();
        }

        $result['university'] = $university;
        $result['universityPrices'] = $universityPrices;

        return response()->json($result);
    }

    public function emailValidate(Request $request)
    {
        return response()->json(!SomsClient::where('email', $request->get('email'))->exists());
    }

    public function promotionCodeValidate(Request $request)
    {
        Log::debug($request->get('promotion_code'));
        $now = Carbon::now();

        $somsPromotion = SomsPromotion::with('items')->where('code', $request->get('promotion_code'))->whereDate('effective_from', '<=', $now->toDateString())->whereDate('effective_to', '>=', $now->toDateString());
        if ($somsPromotion->exists()) {
            Log::debug('exist');
            return $somsPromotion->first();
        } else {
            Log::debug('not found');
            return response()->json(false);
        }
    }

    public function sendemailOtp(Request $request){
        $request->validate([
            'email'=>'required|email'
        ]);
        $email = $request->email;
        $user = SomsClient::where('email', $email)->first();
        if($user){
            $otp = rand(1111,99999);
            $otpData = new OtpMsg;
            $otpData->otp = $otp;
            $otpData->email_id = $email;
            $otpData->save();
            $this->sendOtpEmail($otp, $user);
            return response()->json(['success'=>true,'msg'=>'OTP sent on your email, please check'], 200);
        }else{
            return response()->json(['error'=>true,'msg'=>'This email id is not registered'], 404);
        }

    }

    public function validateOtp(Request $request){
        $request->validate([
            'email'=>'required|email',
            'otp'=>'required|integer',
        ]);
        $email = $request->email;
        $otp = $request->otp;
        $otpData = OtpMsg::where(['email_id'=>$email, 'otp'=>$otp, 'status'=>0])->first();
        if($otpData){

            return response()->json(['success'=>true,'msg'=>'OTP Validate'], 200);
        }else{
            return response()->json(['error'=>true,'msg'=>'Invalid OTP'], 404);
        }


    }

    public function changePassword(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
            'otp'=>'required|integer',
        ]);
        $email = $request->email;
        $otp = $request->otp;
        $otpData = OtpMsg::where(['email_id'=>$email, 'otp'=>$otp, 'status'=>0])->first();
        if($otpData){
            $user = SomsClient::where('email', $email)->first();
            if($user){
                $user->password = Hash::make($request->password);
                $user->save();

                $otpData->status = 1;
                $otpData->save();

                return response()->json(['success'=>true,'msg'=>'Password changed'], 200);
            }
            return response()->json(['error'=>true,'msg'=>'Something wrong'], 404);

        }else{
            return response()->json(['error'=>true,'msg'=>'Something wrong'], 404);
        }
    }

    public function sendOtpEmail($otp, $user){
        $email = $user->email;
        $maildata=array(
            'name'=>$user->name,
            'email'=>$email,
            'otp'=>$otp,
        );
        Mail::send('emails.otp', $maildata, function ($message) use ($email)
        {
            $message->from( env('MAIL_FROM_ADDRESS') , 'ubox');
            $message->subject('OTP for reset password - ubox');
            $message->to($email);
        });
    }
}
