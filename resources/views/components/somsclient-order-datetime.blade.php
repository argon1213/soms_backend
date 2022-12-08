<?php
  $date_title = $type.'_date_other';
  $time_title = $type.'_time_other';

  $cutoff = $model->{$type.'_cutoff'};

  $minDay = Carbon\Carbon::today()->addDays(2)->toDateString();
?>

<div class="row form-group">
  <div class="col-md-8 mb-3 mb-md-0">
    <label class="font-weight-bold" for="{{ $date_title }}">
      {{ __('somsorder.'.$date_title)  }}
    </label>
    <input class="form-control" type="date" id="{{ $date_title }}" name="{{ $date_title }}" value="{{ $model->{$date_title} }}" {{ $cutoff ? "readonly":"" }} min="{{ $minDay }}">
  </div>
  <div class="col-md-4 mb-3 mb-md-0">
    <label class="font-weight-bold" for="{{ $time_title }}">
      {{ __('somsorder.'.$time_title)  }}
    </label>
    <select class="form-control form-control-sm" id="{{ $time_title }}" name="{{ $time_title }}" {{ $cutoff ? "disabled":"" }}>
      <option value="09:00am - 02:00pm" {{ ($model->{$time_title} == '09:00am - 12:00noon')?'selected':'' }}>09:00am - 02:00pm</option>
      <option value="02:00pm - 06:00pm" {{ ($model->{$time_title} == '02:00pm - 05:00pm')?'selected':'' }}>02:00pm - 06:00pm</option>
    </select>
  </div>
</div>
