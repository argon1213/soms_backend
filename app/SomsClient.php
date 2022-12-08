<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SomsClient extends Authenticatable
{
  use SoftDeletes;

    public function city(){
        return $this->belongsTo(SomsClient::class, 'city_id');
    }

    public function state(){
        return $this->belongsTo(SomsState::class, 'state_id');
    }

  public function university()
  {
      return $this->belongsTo('App\SomsUniversity', 'university_id');
  }

  public function orders()
  {
      return $this->hasMany('App\SomsOrder', 'client_id');
  }

  public function availableOrders()
  {
      return $this->orders->where('current_version', 1);
  }

  // public function getDefaultPhoneAttribute()
  // {
  //   if($this->mobile_phone_hk != null)
  //     return $this->mobile_phone_hk;
  //   if($this->mobile_phone_cn != null)
  //     return $this->mobile_phone_cn;
  //   return null;
  // }
  /**
   * For Auth !!!! Don't edit unless you know want you are doing
   */
   use Notifiable;

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
       'name', 'email', 'password',
   ];

   /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
   protected $hidden = [
       'password', 'remember_token',
   ];

   /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
   protected $casts = [
       'email_verified_at' => 'datetime',
   ];

   protected $appends = ['orderCount'];

   public function getOrderCountAttribute()
   {
       return $this->availableOrders()->count();
   }
}
