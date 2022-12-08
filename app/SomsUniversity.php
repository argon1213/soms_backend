<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsUniversity extends Model
{
  use SoftDeletes;

  protected $appends = ['display_name', 'label'];

  public function getLabelAttribute()
  {
    return $this->university;
  }

  public function getDisplayNameAttribute()
  {
      return $this->university_alias." - ".$this->university;
  }

  public function clients()
  {
      return $this->hasMany('App\SomsClient', 'university_id');
  }

  public function ordersCount()
  {
      $result = 0;

      foreach ($this->clients as $client) {
        $result += $client->availableOrders()->count();
      }

      return $result;
  }
}
