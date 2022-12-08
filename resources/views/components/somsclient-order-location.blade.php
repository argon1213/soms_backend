<?php
  if($model instanceof App\SomsOrder)
  {
    $lang_prefix    = 'somsorder';
    $location_title = $type.'_location_other';
    $city_title     = $type.'_city_id';
    $state_title    = $type.'_state_id';

    $valid = true;
  }
  else if($model instanceof App\SomsClient)
  {
    $lang_prefix    = 'somsclient';
    $location_title = 'address1';
    $city_title     = 'city_id';
    $state_title    = 'state_id';

    $valid = true;
  }
  else
  {
    $valid = false;
  }
?>
@if($valid)
<div class="row form-group">
  <div class="col-md-12 mb-3 mb-md-0">
    <label class="font-weight-bold" for="{{ $location_title }}">
      {{ __($lang_prefix.'.'.$location_title)  }}
    </label>
    <input class="form-control" type="text" id="{{ $location_title }}" name="{{ $location_title }}" value="{{ old($location_title)? old($location_title):$model->{$location_title} }}">
  </div>
  <div class="col-md-6 mb-3 mb-md-0">
    <label class="font-weight-bold" for="{{ $city_title }}">
      {{ __($lang_prefix.'.'.$city_title)  }}
    </label>
    <select class="form-control" id="{{ $city_title }}" name="{{ $city_title }}">
      @foreach($cities as $city)
        <option value="{{ $city->id }}" {{ ($model->{$city_title} == $city->id)?'selected':'' }}>
          {{ $city->name_cn }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6 mb-3 mb-md-0">
    <label class="font-weight-bold" for="{{ $state_title }}">
      {{ __($lang_prefix.'.'.$state_title)  }}
    </label>
    <select class="form-control" id="{{ $state_title }}" name="{{ $state_title }}">
      @foreach($states as $state)
        <option value="{{ $state->id }}" {{ ($model->{$state_title} == $state->id)?'selected':'' }}>
          {{ $state->name_cn }}
        </option>
      @endforeach
    </select>
  </div>
</div>
@endif
