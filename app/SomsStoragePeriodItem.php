<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsStoragePeriodItem extends Model
{
  public function item()
  {
      return $this->belongsTo('App\SomsItem', 'item_id');
  }
}
