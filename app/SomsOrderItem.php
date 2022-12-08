<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsOrderItem extends Model
{
  use SoftDeletes;

  //protected $fillable = ['item_id', 'item_price'];

  protected $fillable = ['item_id', 'item_qty', 'item_price'];

  protected $appends = ['item_display_name','item_category'];

  public function order()
  {
      return $this->belongsTo('App\SomsOrder', 'order_id');
  }

  public function item()
  {
      return $this->belongsTo('App\SomsItem', 'item_id')->withTrashed();
  }

  public function getItemDisplayNameAttribute()
  {
      return $this->item->display_name;
  }

  public function getItemCategoryAttribute()
  {
      return $this->item->category;
  }

  // public function price()
  // {
  //     return $this->belongsTo('App\SomsItemPrice', 'item_price_id');
  // }

  // protected $appends = ['display_price'];

  // public function getDisplayPriceAttribute()
  // {
  //     $specialPrice = $this->item->specialPrice($this->order->client->university_id);
  //     return ($specialPrice == null)? $this->item->price:$specialPrice->price;
  // }

  public function total()
  {
      return $this->item_price * $this->item_qty;
  }

  public function calcBestPrice($storage_period_id, $promotion_id)
  {
    $bestPrice = $this->item->price;
    if($storage_period_id != null)
    {
      $storagePeriodItem = SomsStoragePeriodItem::where('storage_period_id', $storage_period_id)->where('item_id', $this->item->id)->first();
      $bestPrice = ($storagePeriodItem)? $storagePeriodItem->price: $bestPrice;
    }

    if($promotion_id != null)
    {
      $promotionItem = SomsPromotionItem::where('promotion_id', $promotion_id)->where('item_id', $this->item->id)->first();
      $bestPrice = ($promotionItem && $promotionItem->price < $bestPrice)? $promotionItem->price: $bestPrice;
    }

    return $bestPrice;
  }
}
