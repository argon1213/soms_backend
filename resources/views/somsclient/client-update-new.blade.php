@extends('layouts.vendor')

@section('title')
@lang('navbar.somsclient.client.update')
@stop

@section('banner')
  @component('components.somsclient-banner', ['routeLink'=>['somsclient.client.update']])
  @endcomponent
@stop

@section('content')
<div class="container">
  <div class="row">
    <div class="col-lg-4">
      <div class="p-4 mb-3 bg-white">
        <p class="h4 text-black mb-3 font-weight-bold">個人資料(不可修改)</p>

        <p class="mb-0 font-weight-bold">姓名 Name</p>
        <p class="mb-4">{{ $client->name }}</p>

        <p class="mb-0 font-weight-bold">電郵地址 Email Address</p>
        <p class="mb-4">{{ $client->email }}</p>

        @if($client->university_id != null && $client->university_id > 0)
          <p class="mb-0 font-weight-bold">就讀大學 University</p>
          <p class="mb-4">{{ $client->university->display_name }}</p>
        @endif
      </div>
    </div>

    <div class="col-md-12 col-lg-8 mb-5">
      <form method="post" action="{{ route('somsclient.client.update.submit') }}" class="p-5 bg-white">
        @csrf
        <input type="hidden" id="id" name="id" value="{{ $client->id }}">

        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-0">
            <label class="font-weight-bold" for="contact">手機號碼 Mobile No. *</label>
            <input type="text" id="contact" name="contact" class="form-control" placeholder="8 to 13 digits" value="{{ old('contact')? old('contact'):$client->contact }}">
          </div>
        </div>

        @component('components.somsclient-order-location', ['type'=>'','model'=>$client,'cities'=>$cities,'states'=>$states])
        @endcomponent

        @if($client->university_id != null && $client->university_id > 0)
          <div class="row form-group">
            <div class="col-md-12 mb-3 mb-md-0">
              <label class="font-weight-bold" for="student_id">學生證號碼 Student ID No.</label>
              <input type="text" id="student_id" name="student_id" class="form-control" value="{{ old('student_id')? old('student_id'):$client->student_id }}">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-md-12 mb-3 mb-md-0">
              <label class="font-weight-bold" for="wechat">Wechat ID *</label>
              <input type="text" id="wechat" name="wechat" class="form-control" value="{{ old('wechat')? old('wechat'):$client->wechat }}">
            </div>
          </div>
        @endif

        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-1">
            <label class="font-weight-bold" for="password_new">更改密碼 Change Password *</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="舊密碼" value="">
            <input type="password" id="password_new" name="password_new" class="form-control" placeholder="新密碼" value="">
          </div>
          <div class="col-md-12 mb-3 mb-md-0">
            <input type="password" id="password_new_confirmation" name="password_new_confirmation" class="form-control" placeholder="確認新密碼" value="">
          </div>
        </div>
<!--
        <div class="row form-group">
          <div class="col-md-12 mb-3 mb-md-1">
            <label class="font-weight-bold" for="address1">密碼 Password *</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="必須輸入密碼以更新個人資料" value="">
          </div>
        </div>
 -->
        <div class="row form-group">
          <div class="col-md-12">
            <input type="submit" value="確認更改" class="btn btn-primary  py-2 px-5">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@stop
