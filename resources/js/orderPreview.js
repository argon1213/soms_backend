var monthlyPrice, otherPrice, deliveryServiceFee, deliveryServiceTotal, storageMonth, haveOtherLocOrDate;

function resetOrderPreview(){
  //
  resetAccountPreview();
  //
  resetProductPreview();
  //
  resetCheckinoutPreview();
  //
  resetSummaryPreview();
}

function resetAccountPreview(){
  $('#preview-account').html('');
}

function createAccountPreview(){
  var previewAccountHtml = '';
  var accountDataArray = ['name','email','id_number','mobile_phone_hk','mobile_phone_cn','address1','address2','wechat'];
  accountDataArray.forEach(buildAccountHtml);

  function buildAccountHtml(inputKey) {
    var label = $('label[for='+inputKey+']').text();
    var value = $('#'+inputKey).val();

    previewAccountHtml += '<div class="row"><div class="col-md-8 col-xs-12">'+label+'</div><div class="col-md-4 col-xs-12">'+value+'</div></div>';
  }

  $('#preview-account').append(previewAccountHtml);
}

function resetProductPreview(){
  $('#preview-product').html('');
}

function createProductPreview(){
  var previewProductHtml = '<div class="row">';
  previewProductHtml += '<div class="col-md-8 col-xs-12">產品</div>';
  previewProductHtml += '<div class="col-md-2 col-xs-6">數量</div>';
  previewProductHtml += '<div class="col-md-2 col-xs-6">單價</div>';
  previewProductHtml += '</div>';

  var boxCount = 0;
  $(".product-item").each(function() {
      var itemId = $(this).data('id');
      var itemCategory = $(this).data('category');

      var itemNameLabel = $('#product-'+itemId+'-name').text();
      var itemQtyInput = $('#product-qty-'+itemId).val();
      var itemPriceLabel = $('#product-'+itemId+'-price').text();

      var itemQty = parseFloat(itemQtyInput);

      if(itemQty > 0)
      {
        if(itemCategory === 'box')
          boxCount += itemQty;

        previewProductHtml += '<div class="row">';
        previewProductHtml += '<div class="col-md-8 col-xs-12"><label id="preview-product-'+itemId+'-name">'+itemNameLabel+'</label></div>';
        previewProductHtml += '<div class="col-md-2 col-xs-6 text-right"><label id="preview-product-'+itemId+'-qty">'+itemQtyInput+'</label></div>';
        previewProductHtml += '<div class="col-md-2 col-xs-6 text-right"><label id="preview-product-'+itemId+'-price">HKD'+itemPriceLabel+'</label></div>';
        previewProductHtml += '</div>';
      }
  });
  // var subTotal = parseFloat($('#total-price').text());
  monthlyPrice = parseFloat($('#monthly-price').text());
  otherPrice = parseFloat($('#other-price').text());
  // console.log('Box Count : '+boxCount);
  deliveryServiceFee = parseFloat($('#delivery-service-fee-label').text());
  deliveryServiceTotal = (boxCount > 4)?(deliveryServiceFee * boxCount + deliveryServiceFee):(deliveryServiceFee * 4 + deliveryServiceFee);
  // console.log('Delivery Service Total : '+deliveryServiceTotal);
  previewProductHtml += '<div class="row">';
  previewProductHtml += '<div class="col-md-6 col-xs-12">小計 Subtotal</div>';
  previewProductHtml += '<div class="col-md-6 col-xs-12 text-right"><label id="preview-product-monthly-price">每月 HKD'+monthlyPrice+'</label></div>';
  previewProductHtml += '</div>';

  previewProductHtml += '<div class="row">';
  previewProductHtml += '<div class="col-md-6 col-xs-12"></div>';
  previewProductHtml += '<div class="col-md-6 col-xs-12 text-right"><label id="preview-product-other-price">另加 HKD'+otherPrice+'</label></div>';
  previewProductHtml += '</div>';

  $('#preview-product').append(previewProductHtml);
}

function resetCheckinoutPreview(){
  $('#preview-checkin').html('');
  $('#preview-checkout').html('');
}

function createCheckinoutPreview(){
  var checkinoutDataArray = ['location','location_other','date',['date_other','time_other']];

  var previewCheckinHtml = '';
  var previewCheckoutHtml = '';
  haveOtherLocOrDate = false;
  //
  var checkindate = '';
  var checkoutdate = '';

  checkinoutDataArray.forEach(buildCheckinoutHtml);

  function buildCheckinoutHtml(inputKey) {
    previewCheckinHtml += buildCheckinoutRowHtml('checkin', inputKey);
    previewCheckoutHtml += buildCheckinoutRowHtml('checkout', inputKey);
  }

  function buildCheckinoutRowHtml(prefix, inputKey){
    console.log(prefix+':'+inputKey);
    if(Array.isArray(inputKey)){
      var label = $('#'+prefix+'_'+inputKey[0]+'_label').text();
      var value = '';
      for(var i = 0; i < inputKey.length; i++){
        var disable = $('#'+prefix+'_'+inputKey[i]).is(':disabled');
        if(disable)
          continue;
        if(value != '')
          value += ' ';
        value += $('#'+prefix+'_'+inputKey[i]).val().trim();
        if(inputKey[i].includes("other") && value != '')
          haveOtherLocOrDate = true;
        if('checkin' == prefix && inputKey[i].includes("date") && value != '')
          checkindate = value;
        if('checkout' == prefix && inputKey[i].includes("date") && value != '')
          checkoutdate = value;
      }
      console.log(label+' '+value);
      if(value === '')
        return '';
    }else{
      var key = prefix+'_'+inputKey;
      var label = $('#'+key+'_label').text();
      var value = $('#'+key).val();
      var disable = $('#'+key).is(':disabled');
      if(disable)
        return '';
      if($('#'+key).is("select"))
      {
        if(value != '')
          value = $('#'+key+' option:selected').text().trim();
      }
      console.log(label+' '+value);
      if(value === '')
        return '';
      if(inputKey.includes("other"))
        haveOtherLocOrDate = true;
      if('checkin' == prefix && inputKey.includes("date") && value != '')
        checkindate = value.split(" ")[0];
      if('checkout' == prefix && inputKey.includes("date") && value != '')
        checkoutdate = value.split(" ")[0];
    }
    return '<div class="row"><div class="col-md-6 col-xs-12">'+label+'</div><div class="col-md-6 col-xs-12 text-right">'+value+'</div></div>';
  }

  storageMonth = calcDateDiffByMonth(checkindate,checkoutdate);
  console.log(checkindate+':'+checkoutdate+':'+storageMonth);

  $('#preview-checkin').append(previewCheckinHtml);
  $('#preview-checkout').append(previewCheckoutHtml);

  if(haveOtherLocOrDate){
    $('#delivery-service-remark').show();
  }
}

function resetSummaryPreview(){
  $('#preview-summary').html('');
  $('#delivery-service-remark').hide();

  $('#product-total-fee').val(0);
  $('#delivery-service-fee').val(0);
  $('#total-fee').val(0);
}

function createSummaryPreview(){
  var previewSummaryHtml = '';
  var total = 0;
  var subTotal = (monthlyPrice * storageMonth) + otherPrice;

  previewSummaryHtml += '<div class="row">';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12">產品收費 Product Fee</div>';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>每月 HKD'+monthlyPrice+'</label></div>';
  previewSummaryHtml += '</div>';

  previewSummaryHtml += '<div class="row">';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12"></div>';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>儲存期(月):'+storageMonth+'</label></div>';
  previewSummaryHtml += '</div>';

  previewSummaryHtml += '<div class="row">';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12"></div>';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>另加 HKD'+otherPrice+'</label></div>';
  previewSummaryHtml += '</div>';

  previewSummaryHtml += '<div class="row">';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12"></div>';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>HKD:'+subTotal+'</label></div>';
  previewSummaryHtml += '</div>';

  total += subTotal;

  $('#product-total-fee').val(subTotal);
  $('#storage-month').val(storageMonth);

  if(haveOtherLocOrDate){
    previewSummaryHtml += '<div class="row">';
    previewSummaryHtml += '<div class="col-md-6 col-xs-12">外送服務 額外收費 Delivery Service Extra Fee</div>';
    previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>HKD'+deliveryServiceTotal+'</label></div>';
    previewSummaryHtml += '</div>';

    total += deliveryServiceTotal;

    $('#delivery-service-fee').val(deliveryServiceTotal);
  }
  else{
    $('#delivery-service-fee').val(0);
  }

  previewSummaryHtml += '<div class="row">';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12">總收費 Total Fee</div>';
  previewSummaryHtml += '<div class="col-md-6 col-xs-12 text-right"><label>HKD'+total+'</label></div>';
  previewSummaryHtml += '</div>';

  $('#total-fee').val(total);

  $('#preview-summary').append(previewSummaryHtml);
}

function createOrderPreview(){
  //
  createAccountPreview();
  //
  createProductPreview();
  //
  createCheckinoutPreview();
  //
  createSummaryPreview();
}
// For createCheckinoutPreview
function calcDateDiffByMonth(d1Str, d2Str){
  var d1 = new Date(d1Str);
  var d2 = new Date(d2Str);

  var yearDiff = d2.getFullYear() - d1.getFullYear();
  var monthDiff = d2.getMonth() - d1.getMonth();
  var dayDiff = d2.getDate() - d1.getDate();

  var result = (yearDiff * 12) + monthDiff + ((dayDiff > 0)?1:0);
  console.log(yearDiff+':'+monthDiff+':'+dayDiff+':'+result);
  return result;
}
