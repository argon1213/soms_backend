<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SomsPaymentType extends Model
{
  use SoftDeletes;

  const CREDIT_CARD     = 3;
  const WECHATPAY       = 4;
  const ALIPAY          = 5;
  const CASH            = 6;
}
