@extends('layouts.vendor')

@section('title')
@lang('navbar.somsclient.dashboard')
@stop

@section('banner')
  @component('components.somsclient-banner', ['routeLink'=>['somsclient.dashboard']])
  @endcomponent
@stop

@section('banner2')

@stop

@section('content')
<div class="container">
  <div class="row justify-content-start text-left mb-5">
    <div class="col-md-12" data-aos="fade">
      <h2 class="font-weight-bold text-black">尊敬的 {{ Auth::guard('somsclient')->user()->name }} 先生/女士，您好！歡迎登錄 @lang('common.company.nickname') 官方網站。</h2>
    </div>
  </div>
  <div class="row justify-content-start text-left mb-5">
    <div class="col-md-9" data-aos="fade">
      <h2 class="font-weight-bold text-black">Recent Orders 交易記錄</h2>
    </div>
    <div class="col-md-3" data-aos="fade" data-aos-delay="200">
      <a href="{{ route('index') }}" class="btn btn-primary py-3 btn-block"><span class="h5">+</span>@lang('somsclient.create.order')</a>
    </div>
  </div>
  @foreach($orders as $order)
    <div class="row" data-aos="fade">
     <div class="col-md-12">
       <div class="job-post-item bg-white p-4 d-block d-md-flex align-items-center">

         <div class="mb-4 mb-md-0 mr-5">
          <div class="job-post-item-header d-flex align-items-center">
            <h2 class="mr-3 text-black h4">{{ $order->code }}</h2>
          </div>
          <div class="job-post-item-header d-flex align-items-center">
            <div class="badge-wrap">
             <span class="bg-primary text-white badge py-2 px-4">Fee HKD${{ $order->total_fee }}</span> &nbsp;
            </div>
            <div class="badge-wrap">
             <span class="bg-primary text-white badge py-2 px-4">Paid HKD${{ $order->paid_fee }}</span> &nbsp;
            </div>
            <div class="badge-wrap">
             <span class="bg-primary text-white badge py-2 px-4">Balance HKD${{ $order->total_fee - $order->paid_fee }}</span>
            </div>
          </div>
          <div class="job-post-item-body d-block d-md-flex">
            <div class="mr-3">
              <span class="fl-bigmug-line-portfolio23"></span>
              <span>Pay By {{ $order->paymentType->description }}</span>
            </div>
            <div>
              <span class="fl-bigmug-line-note35"></span>
              <span>{{ $order->status->description }}</span>
            </div>
          </div>
          <div class="job-post-item-body d-block d-md-flex">
            <div class="mr-3">
              <span class="fl-bigmug-line-shopping202"></span>
              <span>{{ $order->created_at }}</span>
            </div>
          </div>
         </div>
         <div class="ml-auto">
          <a href="{{ route('somsclient.order.update',['id'=>$order->id]) }}" class="btn btn-primary py-2">@lang('somsclient.update.order')</a>
          <a href="{{ route('somsclient.payment-invoice.send',['id'=>$order->id]) }}" class="btn btn-secondary py-2" style="background-color:#e67176; color:white;">@lang('somsclient.payment-invoice.send')</a>
          <a href="{{ route('somsclient.payment-invoice.view',['id'=>$order->id]) }}" class="btn btn-success py-2" style="background-color:#5ec4b6; color:white;">@lang('somsclient.payment-invoice.view')</a>
         </div>
       </div>
       <div class="job-post-item bg-white p-4 d-block d-md-flex align-items-center">                                                         
          <span>{{ 'QR Code : '.$order->remark_qrcode }}</span>
       </div>
     </div>
    </div>
  @endforeach
  <div class="justify-content-center mt-3" style="display:flex;">
    {{ $orders->links() }}
  </div>
</div>
@stop

@section('content2')

@stop

@section('page-css')

@stop
