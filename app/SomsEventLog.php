<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SomsEventLog extends Model
{
  public function eventType()
  {
      return $this->belongsTo('App\SomsEventType', 'event_type_id');
  }
}
