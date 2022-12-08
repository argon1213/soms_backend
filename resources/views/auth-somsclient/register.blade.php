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
          <span>請填寫以下資料完成開戶</span>
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
              <span class="form-sub-header-text">基本資料</span>
            </div>
            <div class="form-sub-detail">
              <div class="container p-0">
                @component('components.register-row',
                ['id'=>'name','name'=>'name','label'=>'會員名','mandatory'=>true,'placeholder'=>'請填寫會員名','input'=>'text','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'email','name'=>'email','label'=>'電子郵件','mandatory'=>true,'placeholder'=>'請填寫電子郵件','input'=>'email','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'agent_code','name'=>'agent_code','label'=>'代碼','mandatory'=>true,'placeholder'=>'請填寫代碼','input'=>'text','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'password','name'=>'password','label'=>'密碼','mandatory'=>true,'placeholder'=>'請填寫密碼','input'=>'password','remark'=>''])
                @endcomponent
                @component('components.register-row',
                ['id'=>'password_confirmation','name'=>'password_confirmation','label'=>'確認密碼','mandatory'=>true,'placeholder'=>'請填寫確認密碼','input'=>'password','remark'=>''])
                @endcomponent
              </div>
            </div>
            <div class="form-sub-header">
              <span class="form-sub-header-text">申請所需文件</span>
            </div>
            <div class="form-sub-detail">
              <div class="container p-0">
                <!-- 身份證 -->
                @component('components.register-row',
                ['id'=>'photo_id_card','name'=>'photo_id_card','label'=>'身份證','mandatory'=>true,'placeholder'=>'按此上傳身份證','input'=>'image','remark'=>''])
                @endcomponent
                <!-- 住址證明 -->
                @component('components.register-row',
                ['id'=>'photo_address_proof','name'=>'photo_address_proof','label'=>'住址證明','mandatory'=>true,'placeholder'=>'按此上傳住址證明','input'=>'image','remark'=>''])
                @endcomponent
              </div>
            </div>
            <div class="form-sub-header">
              <div class="form-header-text">
                <button type="submit" class="btn btn-dark" style="width: 300px; max-width: calc(100% - 40px);">
                  提交申請
                </button>
              </div>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
@stop
