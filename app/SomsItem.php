<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsItem extends Model
{
  use SoftDeletes;

  protected $appends = ['display_name','default_price'];

  public function getDisplayNameAttribute()
  {
      return $this->name." - ".$this->name_cn;
  }

  public function getDefaultPriceAttribute()
  {
    return (int)$this->price;
    // $defaultStoragePeriod = SomsStoragePeriod::where('default_select', 1)->first();
    // if($defaultStoragePeriod)
    // {
    //   $storagePeriodPrice = SomsStoragePeriodItem::where('storage_period_id', $defaultStoragePeriod->id)->where('item_id', $this->id)->first();
    //   return ($storagePeriodPrice)? (int)$storagePeriodPrice->price: (int)$this->price;
    // }
    // else
    // {
    //   // No Default Storage Period - Get Original Price
    //   return (int)$this->price;
    // }
  }

  public function images()
  {
    return $this->hasMany('App\SomsItemImage', 'item_id');
  }

  // public function prices()
  // {
  //     return $this->hasMany('App\SomsItemPrice', 'item_id');
  // }
  //
  // public function specialPrice($uid)
  // {
  //     return $this->prices->where('university_id', $uid)->first();
  // }
}
