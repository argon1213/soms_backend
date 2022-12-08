<div class="unit-5 overlay" style="background-image: url({{ asset('vendor/authsomsclient/images/hero_bg_2.jpg') }});">
  <div class="container text-center">
    <h2 class="mb-0">@lang('navbar.'.end($routeLink))</h2>
    <p class="mb-0 unit-6">
      <a href="{{ route('index') }}">@lang('navbar.somsclient.book')</a>
      @foreach($routeLink as $route)
        <span class="sep">></span>
        @if($loop->last)
          <span>@lang('navbar.'.$route)</span>
        @else
          <a href="{{ route($route) }}">@lang('navbar.'.$route)</a>
        @endif
      @endforeach
    </p>
  </div>
</div>
