@hasSection('register-row-style')
@else
  @section('register-row-style')
    <style>
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

      .form-input-label-container {
        text-align: left;
      }
        @media (min-width: 1024px) {
          .form-input-label-container {
            text-align: right;
          }
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
        width: auto;
        /* color: rgba(255, 255, 255, 0.8); */
        color: #26baee;
      }

      .form-input-image-label {
        background-color: white;
        border-radius: 4px;
        width: 100%;
        height: 150px;
        text-align: center;
        padding-top: 70px;
        border: 1px solid #ced4da;
        font-family: MicrosoftYaHei;
        font-size: 14px;
        color: #636c72;
      }

      .form-input-image-placeholder {
        font-family: MicrosoftYaHei;
        font-size: 14px;
        /* text-align: justify; */
        /* color: #aaaaaa; */
        color: #636c72;
        position: absolute;
        top:50%;
        left:40%;
      }

      .form-input-signature-label-top {
        background-color: white;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        width: 100%;
        height: auto;
        text-align: center;
      }

      .form-input-signature-label-bottom {
        background-color: white;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        width: 100%;
        height: auto;
        text-align: center;
      }
    </style>
  @stop

  @yield('register-row-style')
@endif

<div class="row form-sub-detail-row">
  @if($input === 'checkbox')
    <div class="col-lg-4 col-sm-12 form-input-label-container">

    </div>
    <div class="col-lg-4 col-sm-12">
      <input class="form-input-checkbox" type="checkbox" id="{{ $id }}" name="{{ $name }}" {{ ((isset($mandatory) && $mandatory)?'required':'')  }}>
      <label class="form-sub-detail-text" for="{{ $id }}">{{ $label }}</label>
    </div>
    <div class="col-lg-4 col-sm-12">

    </div>
  @elseif($input === 'select')
    <div class="col-lg-4 col-sm-12 form-input-label-container">
      <label class="form-input-label" for="{{ $id }}">
        @if($mandatory)
          <span style="color: rgba(218, 165, 30, 0.8);">*</span>
        @endif
        {{ $label }}
      </label>
    </div>
    <div class="col-lg-4 col-sm-12">
      <select class="form-control w-100" id="{{ $id }}" name="{{ $name }}" {{ ((isset($mandatory) && $mandatory)?'required':'')  }}>
        @if(isset($options) && sizeof($options) > 0)
          @foreach($options as $optionValue => $optionName)
            <option value="{{ $optionValue }}" {{ (isset($default) && ($default === $optionValue)?'selected':'') }}>{{ $optionName }}</option>
          @endforeach
        @else
            <option>No Options. Please check.</option>
        @endif
      </select>
    </div>
    <div class="col-lg-4 col-sm-12">

    </div>
  @elseif($input === 'image')
    <div class="col-lg-4 col-sm-12 form-input-label-container">
      <label class="form-input-label" for="{{ $id }}">
        @if($mandatory)
          <span style="color: rgba(218, 165, 30, 0.8);">*</span>
        @endif
        {{ $label }}
      </label>
    </div>
    <div class="col-lg-4 col-sm-12">
      <!-- <label class="form-input-image-placeholder" id="{{ $id }}_file_info">{{ $placeholder }}</label> -->
      <label class="form-input-image-label" id="{{ $id }}_upload_btn">
        <input type="file" class="form-control w-100" id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" {{ ((isset($mandatory) && $mandatory)?'required':'')  }} style="display:none;">
        <!-- <img src="/images/modern/watermark-image-input.png"
           srcset="/images/modern/watermark-image-input@2x.png 2x,
                   /images/modern/watermark-image-input@3x.png 3x"> -->
        <label id="{{ $id }}_file_info">{{ $placeholder }}</label>
      </label>
      <script>
        if(dataAry === undefined)
          var dataAry = {};

          dataAry['{{ $id }}_upload_btn'] = document.getElementById('{{ $id }}_upload_btn');
          dataAry['{{ $id }}_file_info'] = document.getElementById('{{ $id }}_file_info');
          dataAry['{{ $id }}'] = document.getElementById('{{ $id }}');

          dataAry['{{ $id }}_file_info'].addEventListener("click", function(){
            dataAry['{{ $id }}_upload_btn'].click();
          });

          dataAry['{{ $id }}'].addEventListener('change', () => {
            const name = dataAry['{{ $id }}'].value.split(/\\|\//).pop();
            const truncated = name.length > 20 ? '...'+name.substr(name.length - 20) : name;

          dataAry['{{ $id }}_file_info'].innerHTML = truncated;
        });
      </script>
    </div>
    <div class="col-lg-4 col-sm-12">

    </div>
  @elseif($input === 'signature')
    <div class="col-lg-4 col-sm-12 form-input-label-container">
      <label class="form-input-label" for="{{ $id }}">
        @if($mandatory)
          <span style="color: rgba(218, 165, 30, 0.8);">*</span>
        @endif
        {{ $label }}
      </label>
    </div>
    <div class="col-lg-4 col-sm-12">
      <label class="form-input-signature-label-top" style="margin-bottom:0px;">
        <input type="hidden" id="digital_signature" name="digital_signature" {{ ((isset($mandatory) && $mandatory)?'required':'')  }}>
        <div id="signature-pad-container">
        </div>
        <!-- <canvas id="signature-pad" class="signature-pad mw-100" width=400 height=200 style="border:1px #aaaaaa;"></canvas> -->
      </label>
      <label class="form-input-signature-label-bottom">
        <button id="clear" class="btn btn-gold m-3" type="button" style="width:100px;">清除</button>
      </label>
    </div>
    <div class="col-lg-4 col-sm-12">

    </div>
  @else
    <div class="col-lg-4 col-sm-12 form-input-label-container">
      <label class="form-input-label" for="{{ $id }}">
        @if($mandatory)
          <span style="color: rgba(218, 165, 30, 0.8);">*</span>
        @endif
        {{ $label }}
      </label>
    </div>
    <div class="col-lg-4 col-sm-12">
      <input type="{{ $input }}" class="form-control w-100" id="{{ $id }}" name="{{ $name }}" value="{{ old($name) }}" placeholder="{{ $placeholder }}" {{ ((isset($mandatory) && $mandatory)?'required':'')  }}>
    </div>
    <div class="col-lg-4 col-sm-12">
      <span class="form-sub-detail-text">{{ $remark }}</span>
    </div>
  @endif
</div>
