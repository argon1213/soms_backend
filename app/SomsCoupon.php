<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsCoupon extends Model
{
  use SoftDeletes;

  public function university()
  {
      return $this->belongsTo('App\SomsUniversity', 'university_id');
  }
}
