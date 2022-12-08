<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;

 
use App\Http\Controllers\Controller;
use App\User;

use App\SomsClient;
use App\SomsUniversity;
use App\SomsOrder;
use App\SomsStoragePeriod;
use App\SomsPromotion;
use App\SomsItem;
use App\SomsOrderPayment;
use App\SomsPaymentStatus;

 
class ApiAdminController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */

    public function index()
    {
        $universities = SomsUniversity::get();

        foreach ($universities as $university) {
          $university['ordersCount'] = $university -> ordersCount();
        }

        return response()->json($universities);
    }

    public function fetchClients()
    {
      $clients = SomsClient::get();
      return response()->json($clients);
    }

    public function fetchPeriods()
    {
      $periods = SomsStoragePeriod::get();
      return response()->json($periods);
    }

    public function fetchPromotions()
    {
      $promotions = SomsPromotion::get();
      return response()->json($promotions);
    }

    public function fetchPayments()
    {
      $payments = SomsOrderPayment::get();
      return response()->json($payments);
    }
}