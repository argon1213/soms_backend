<div class="col-10 col-xl-10 d-none d-xl-block">
  <nav class="site-navigation text-right" role="navigation">

    <ul class="site-menu js-clone-nav mr-auto d-none d-lg-block">
      <li class="active"><a href="{{ route('index') }}">@lang('navbar.somsclient.book')</a></li>
      @guest('somsclient')
        <li><a href="{{ route('somsclient.login') }}">@lang('navbar.somsclient.login')</a></li>
      @else
        <li><a href="{{ route('somsclient.dashboard') }}">@lang('navbar.somsclient.dashboard')</a></li>
        <li><a href="{{ route('somsclient.client.update') }}">@lang('navbar.somsclient.client.update')</a></li>
        <li><a href="{{ route('somsclient.logout') }}">@lang('navbar.somsclient.logout')</a></li>
      @endguest
    </ul>
  </nav>
</div>
