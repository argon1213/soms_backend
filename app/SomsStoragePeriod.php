<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsStoragePeriod extends Model
{
  use SoftDeletes;

  public function items()
  {
      return $this->hasMany('App\SomsStoragePeriodItem', 'storage_period_id');
  }
}
