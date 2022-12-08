@extends('layouts.vendor')

@section('title')
@lang('navbar.somsclient.login')
@stop

@section('banner')
  @component('components.somsclient-banner', ['routeLink'=>['somsclient.login']])
  @endcomponent
@stop

@section('validator')

@stop

@section('content')
  <style>  #loader{display: none}</style>
  <link rel="stylesheet" href="{{ asset('vendor/slick/custom.css') }}">
  <div class="site-section bg-white">
    <div class="container">
      <div class="row">
        <div class="col-md-2 col-sm-12">

        </div>
        <div class="col-md-8 col-sm-12 mb-5 mx-0 px-0">
          <form class="form-horizontal needs-validation p-5 bg-white" method="POST" action="{{ route('somsclient.login.submit') }}">
            @csrf

            <div class="form-group row">
                <label for="email" class="form-input-label col-lg-4 col-form-label text-lg-right">@lang('common.email')</label>

                <div class="col-lg-6">
                    <input id="email"
                            type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                            name="email" value="{{ old('email') }}" autofocus>

                    @error('email')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="form-input-label col-lg-4 col-form-label text-lg-right">@lang('common.password')</label>

                <div class="col-lg-6">
                    <input id="password" type="password" name="password"
                      class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}">

                    @error('password')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-3">
                <div class="justify-content-between" style="display: flex; margin:0 50px;">
                  <span style="text-align:left;">
                    <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-detail-text" for="remember">@lang('common.remember')</label>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-3">
                <button type="submit" class="btn btn-primary w-100">
                  @lang('common.submit')
                </button>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-3">
                <a href="#" class="clink text-center" data-toggle="modal" data-target="#ResetModl"> @lang('common.resetPassword')</a>
                <div class="changePasssuccess text-success"></div>
              </div>
            </div>
          </form>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="ResetModl" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content modal-sm">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('common.resetPassword')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="form-group">
                    <div class="col-sm-12">
                      <label for="">@lang('common.email')*</label>
                      <input type="email" name="remail" id="remail" class="form-control">
                      <div class="remail-errormsg text-danger"></div>
                    </div>
                  </div>
                  <div class="form-group" id="loader">
                    <div class="col-sm-12 text-center">
                      <img src="{{asset('spinner.gif')}}" style="max-height: 100px">
                    </div>
                  </div>
                  <div class="form-group reBtn">
                    <div class="col-sm-12">
                      <button type="button" class="btn btn-success" onclick="remSubmit()">@lang('common.submit')</button>
                    </div>
                  </div>
                  <div class="form-group otpNo">
                    <div class="col-sm-12">
                      <label for="">@lang('common.OTP')</label>
                      <input type="text" name="rotp" id="rotp" placeholder="Enter OTP" class="form-control">
                      <div class="rotp-errormsg text-danger"></div>
                    </div>
                  </div>
                  <div class="passForm">
                    <div class="form-group">
                      <div class="col-sm-12">
                        <label for="">@lang('common.newPassword')</label>
                        <input type="text" name="rpassword" id="rpassword" placeholder="Enter Password" class="form-control">
                        <div class="rpassword-errormsg text-danger"></div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-12">
                        <label for="">@lang('common.confirmPassword')</label>
                        <input type="text" name="rconfirmpassword" id="rconfirmpassword" placeholder="Enter Password" class="form-control">
                        <div class="rconfirmpassword-errormsg text-danger"></div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-12">
                        <button type="button" class="btn btn-success" onclick="changePassword()">@lang('common.submit')</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-12">

        </div>
      </div>
    </div>
  </div>

@stop
@push('footerscript')
  <script>
    function remSubmit() {
      let remail = $("#remail").val();
      $("#loader").show()
      $("#reBtn").hide()
      if (validateEmail(remail)) {
        $.ajax({
          url: "{{url('ajax/send-email-otp')}}",
          type: 'POST',
          dataType: 'json',
          data: {'email': remail},
          success: function (response) {
            $("#loader").hide()

            $(".otpNo").show()
            $("#remail").prop('readonly', true)
            $(".remail-errormsg").text(response.msg)
          },
          error: function (request, status, error) {
            $(".reBtn").show()
            var myresponse = JSON.parse(request.responseText);
            $(".remail-errormsg").text(myresponse.msg)
          }

        });
      }else{
        $(".remail-errormsg").text('Enter valid email')
      }
    }

    function validateEmail(email) {
      const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    }
    $(function(){
      $('#rotp').keyup(function() {
        let otp = $("#rotp").val()
        let remail = $("#remail").val()
        $.ajax({
          url: "{{url('ajax/validate-otp')}}",
          type: 'POST',
          dataType: 'json',
          data: {'email': remail, 'otp': otp},
          success: function (response) {
            $(".otpNo").hide()
            $(".passForm").show()
            $(".remail-errormsg").text('')
          },
          error: function (request, status, error) {
            var myresponse = JSON.parse(request.responseText);
            $(".remail-errormsg").text(myresponse.msg)
          }

        });
      });
    });

    function changePassword() {
      let otp = $("#rotp").val()
      let email = $("#remail").val()
      let password = $("#rpassword").val()
      let confirmPassword = $("#rconfirmpassword").val()
      if (password != confirmPassword){
        $(".rconfirmpassword-errormsg").text('Password not matched')
      }
      else{
        $.ajax({
          url: "{{url('ajax/change-password')}}",
          type: 'POST',
          dataType: 'json',
          data: {'email': email,'password': password,'otp':otp},
          success: function (response) {
            $("#ResetModl").modal('toggle')
            $(".changePasssuccess").text('Password changed successfully!')
          },
          error: function (request, status, error) {
            var myresponse = JSON.parse(request.responseText);
            $(".rconfirmpassword-errormsg").text(myresponse.msg)
          }

        });
      }
    }

  </script>
  @endpush
