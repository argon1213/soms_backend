<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsPromotion extends Model
{
  use SoftDeletes;

  public function items()
  {
      return $this->hasMany('App\SomsPromotionItem', 'promotion_id');
  }
}
