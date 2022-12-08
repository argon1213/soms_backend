<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsPromotionItem extends Model
{
  protected $fillable = ['item_id', 'price'];

  public function promotion()
  {
      return $this->belongsTo('App\SomsPromotion', 'promotion_id');
  }

  public function item()
  {
      return $this->belongsTo('App\SomsItem', 'item_id');
  }
}
