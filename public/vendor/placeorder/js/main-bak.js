$(function(){
	$("#wizard").steps({
        headerTag: "h4",
        bodyTag: "section",
        transitionEffect: "fade",
        enableAllSteps: false,
        transitionEffectSpeed: 500,
        onStepChanging: function (event, currentIndex, newIndex) {
						if ( newIndex === 1 ) {
								$('.steps ul').addClass('step-2');

								$.post('/ajax/coupon-code/validate', { couponCode: $('#couponCode').val() })
						      .done(function (data){
						        // success data
						        // console.log(data);
										if (data.hasOwnProperty('university_id')) {
											$('#university').val(data.university_id);
										}
						      })
						      .fail(function() {
						        alert( "error" );
						      });
						} else {
								$('.steps ul').removeClass('step-2');
						}
            if ( newIndex === 2 ) {
                $('.steps ul').addClass('step-3');
            } else {
                $('.steps ul').removeClass('step-3');
            }
            if ( newIndex === 3 ) {
                $('.steps ul').addClass('step-4');
            } else {
                $('.steps ul').removeClass('step-4');
            }
						if ( newIndex === 4 ) {
                $('.steps ul').addClass('step-5');
            } else {
                $('.steps ul').removeClass('step-5');
            }

            if ( newIndex === 5 ) {
                $('.steps ul').addClass('step-5');
                $('.actions ul').addClass('step-last');
            } else {
                $('.steps ul').removeClass('step-5');
                $('.actions ul').removeClass('step-last');
            }
            return true;
        },
        labels: {
            finish: "Place Holder",
            next: "Next",
            previous: "Previous"
        }
    });
    // Custom Steps Jquery Steps
    $('.wizard > .steps li a').click(function(){
    	$(this).parent().addClass('checked');
		$(this).parent().prevAll().addClass('checked');
		$(this).parent().nextAll().removeClass('checked');
    });
    // Custom Button Jquery Steps
    $('.forward').click(function(){
    	$("#wizard").steps('next');
    })
    $('.backward').click(function(){
        $("#wizard").steps('previous');
    })
    // Checkbox
    $('.checkbox-circle label').click(function(){
        $('.checkbox-circle label').removeClass('active');
        $(this).addClass('active');
    })
})
