@if(session('message'))
  <div class="bg-light p-3">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="alert {{ session('alert-class', 'alert-info') }}">
            {{ session('message') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endif
