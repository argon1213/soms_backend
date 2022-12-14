@extends('layouts.vendor')

@section('title')
@lang('navbar.somsclient.register')
@stop

@section('banner')
  @component('components.somsclient-banner', ['routeLink'=>['somsclient.register']])
  @endcomponent
@stop

@section('content')
  <style>
    #second-header {
      background-color: #1a1f23 !important;
    }

    .form  {
      width: 100%;
      height: auto;
      /* background-color: rgba(0,0,0,0.3); */
      /* background-color: #343a40; */
      background-color: white;
      margin-left:20px;
      margin-right:20px;
    }

    .form-header-text  {
        font-family: MicrosoftYaHei;
        font-size: 24px;
        color: #26baee;
        text-align: center;
        padding-top: 50px;
        padding-bottom: 50px;
    }

    .form-sub-header {
      border-radius: 4px;
      /* background-color: rgba(0,0,0,0.3); */
      background-color: #26baee;
    }

    .form-sub-header-text {
      font-family: MicrosoftYaHei;
      font-size: 16px;
      line-height: 3.75;
      color: white;
      padding-left: 25px;
      /* color: #999999; */
    }

    .form-sub-detail {
      margin: 50px;
    }

    .form-sub-detail-row {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-sub-detail-text {
      font-family: MicrosoftYaHei;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.8);
    }

    .form-input-label {
      font-family: MicrosoftYaHei;
      font-size: 14px;
      font-weight: normal;
      font-style: normal;
      font-stretch: normal;
      line-height: 4.29;
      letter-spacing: normal;
      text-align: right;
      width: 100px;
      color: rgba(255, 255, 255, 0.8);
    }

    .form-input {
      width: calc(100% - 140px) !important;
      border-radius: 4px;
      border: solid 1px #dddddd;
      background-color: #ffffff;
    }

    .form-input-checkbox
    {
      width: 14px;
      height: 15px;
      border-radius: 2px;
      border: solid 1px #cccccc;
      background-image: linear-gradient(to bottom, #4d8df9, #1662e1);
    }

    .form-detail-text
    {
      font-family: MicrosoftYaHei;
      font-size: 14px;
      line-height: 1.5;
      text-align: justify;
      color: #999999;
    }
  </style>
  <div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <!-- Content -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="form-header-text">
          <span>?????????????????????????????????</span>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 form p-0">
        @if(session()->has('msg'))
            <div class="alert alert-success">
                {{ session()->get('msg') }}
            </div>
        @else
          <form method="POST" action="{{ route('somsclient.register.submit') }}" enctype="multipart/form-data">
            {{ csrf_field() }}

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-sub-header">
              <span class="form-sub-header-text">????????????</span>
            </div>
            <div class="form-sub-detail">
              <div class="container p-0">
                @component('components.register-row',
                ['id'=>'name','name'=>'name','label'=>'?????????','mandatory'=>true,'placeholder'=>'??????????????????','input'=>'text','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'email','name'=>'email','label'=>'????????????','mandatory'=>true,'placeholder'=>'?????????????????????','input'=>'email','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'agent_code','name'=>'agent_code','label'=>'??????','mandatory'=>true,'placeholder'=>'???????????????','input'=>'text','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'password','name'=>'password','label'=>'??????','mandatory'=>true,'placeholder'=>'???????????????','input'=>'password','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'password_confirmation','name'=>'password_confirmation','label'=>'????????????','mandatory'=>true,'placeholder'=>'?????????????????????','input'=>'password','remark'=>''])
                @endcomponent
              </div>
            </div>
            <div class="form-sub-header">
              <span class="form-sub-header-text">??????????????????</span>
            </div>
            <div class="form-sub-detail">
              <div class="container p-0">
                <!-- ????????? -->
                @component('components.register-row',
                ['id'=>'photo_id_card','name'=>'photo_id_card','label'=>'?????????','mandatory'=>true,'placeholder'=>'?????????????????????','input'=>'image','remark'=>''])
                @endcomponent
                <!-- ???????????? -->
                @component('components.register-row',
                ['id'=>'photo_address_proof','name'=>'photo_address_proof','label'=>'????????????','mandatory'=>true,'placeholder'=>'????????????????????????','input'=>'image','remark'=>''])
                @endcomponent
              </div>
            </div>
            <div class="form-sub-header">
              <div class="form-header-text">
                <button type="submit" class="btn btn-dark" style="width: 300px; max-width: calc(100% - 40px);">
                  ????????????
                </button>
              </div>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
@stop
