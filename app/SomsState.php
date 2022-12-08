<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsState extends Model
{
  use SoftDeletes;

  protected $appends = ['display_name'];

  public function getDisplayNameAttribute()
  {
      return $this->name_cn." - ".$this->name;
  }
}
