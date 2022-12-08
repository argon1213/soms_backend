<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

use App\SomsPaymentStatus;

class SomsOrderPayment extends Model
{
    protected $fillable = ['payment_status_id'];

    public static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
          if($model->code == null)
          {
            $generateCode = $model->order->code.'-'.$model->order->payments->count();

            $model->code = $generateCode;
            $model->save();
          }
        });

        static::updated(function($model)
        {

        });

        static::saving(function($model)
        {
          if($model->payment_status_id == SomsPaymentStatus::PAID || $model->payment_status_id == SomsPaymentStatus::CANCELLED){
            if($model->completed_at != null)
              $model->completed_at = Carbon::now();
          }
        });
    }

    public function order()
    {
        return $this->belongsTo('App\SomsOrder', 'order_id');
    }

    // public function client()
    // {
    //     return $this->belongsTo('App\SomsClient', 'client_id');
    // }

    public function type()
    {
        return $this->belongsTo('App\SomsPaymentType', 'payment_type_id');
    }

    public function status()
    {
        return $this->belongsTo('App\SomsPaymentStatus', 'payment_status_id');
    }
}
