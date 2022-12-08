<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsPaymentStatus extends Model
{
  use SoftDeletes;

  const UNPAID    = 1;
  const PAID      = 2;
  const CANCELLED = 3;
}
