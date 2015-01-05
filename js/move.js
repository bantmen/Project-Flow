$( document ).ready(function() {

    var alerts = $('#alerts');
    var timeinterval = 500;
    var flag = 0;


    $('.button2').click(function(){
        alertme();
    });

    function alertme(){
        $('#headertext').text('Please Sign Up to Donate.').css({'backgroundColor': '#FE5353'});
    }

    $('.button1').click(function(){
        alerthide();
        $('#signup').center().css({zIndex:1000}).fadeIn(200).promise().done(function(){
            flag = 1;
        });
    });

    $('.button3').click(function(){
        $('body').scrollTo('.stores', 500);
    });

    function alerthide(){
        $('#headertext').text('The Transparent Donation Platform').css({'backgroundColor': ''});
    }

    jQuery.fn.center = function () {
        this.css("position","fixed");
        this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
            $(window).scrollTop()) + "px");
        this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
            $(window).scrollLeft()) + "px");
        return this;
    };

    $('#container').click(function(){
        if (flag == 1) {
            $('#signup').fadeOut(200).promise().done(function(){
                flag = 0;
            });
        }
    });

    $('select').on('change',function(){
        var optionname = $('#options').find(":selected").text();
        if (optionname == "SMS"){
            $('#phone').removeAttr('disabled');
        }
        else {
            $('#phone').prop('disabled', 'true');
        }
    });





});