/**
 * Created by muhammadmasad on 10/11/2017.
 */



$(document).ready(function () {

    


    $('#art-description').keyup(updateCount);
    $('#art-description').keydown(updateCount);

    function updateCount() {
        var cs = [999- $(this).val().length];
        $('.characters').text(cs);
    }
    

    $('#artPrice').keypress(function(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 8 || charCode == 37) {
            return true;
        } else if (charCode == 46 && $(this).val().indexOf('.') != -1) {
            return false;
        } else if (charCode > 31 && charCode != 46 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });


    $(function() {
        $('.tab3-info label').matchHeight();
    });

$("div .cat-bx").hover(
    function () {
        $(this).addClass("hover-div-category");
    },
    function () {
        $(this).removeClass('hover-div-category');
    }
);
/*Category Selection*/
$("div .cat-bx").click(
    function (e) {
        $("div .cat-bx").children("h4").removeClass("selected-div-category");
        $(this).children("h4").addClass("selected-div-category");
        $('input[name=category]').val($(this).children("img").data("category_id"));
        if($('#Artsubject').val() != ""){
            $("#tabOneValidation").removeClass('validation-pagination');
            $("#tab-2-Next").attr("data-acc_tab","#tab-3");
            $("#tab-2-Next").attr("href","#");
        }
    });

    $('#Artsubject').change(function ()
    {
       if( $('input[name=category]').val() != "" ){
           $("#tabOneValidation").removeClass('validation-pagination');
           $("#tab-2-Next").attr("data-acc_tab","#tab-3");
           $("#tab-2-Next").attr("href","#");
       }
        if($(this).val() == ""){
           $("#tabOneValidation").addClass('validation-pagination');
            $("#tab-2-Next").removeAttr("data-acc_tab","#tab-3");
            $("#tab-2-Next").removeAttr("href","#");

       }

    });

    $(".tab3-bx2  div.btn-yes-no > a ").click(
        function (e) {
            $(".tab3-bx2 div.btn-yes-no > a").removeClass("bool-btn-que");
            $(this).addClass("bool-btn-que");
            $('input[name=is_original]').val($(this).data("is_original"));
        });

    $(".tab3-bx3  div.btn-yes-no > a ").click(
        function (e) {
            $(".tab3-bx3 div.btn-yes-no > a").removeClass("bool-btn-que");
            $(this).addClass("bool-btn-que");
            console.log();
            $('input[name=is_copyright]').val($(this).data("is_copyright"));
        });


    $('#tab-1-Next').hide();
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#art-upload-img3').attr('src', e.target.result);
                $('#art-upload-img2').attr('src', e.target.result);
                $('#art-upload-img').attr('src', e.target.result);


            };
            reader.readAsDataURL(input.files[0]);
        }
        return true;
    }


    $('#art-upload-img2').on('load', function () {
        $(this).cropper({
            preview: '.preview',
            viewMode: 1
        });
        if($(window).width() <= 767){
        $('.t4-left-img').css('width','100%');
            $('.t4-right-img').css('width','100%');
            $('.t4-right-img .preview').css('width','100%');

        }
    });


    $("#uploadArt").change(function(){
        var fileInput = $(this);
        ImageValidator(fileInput);

      });
    $("#inputfile").change(function(){
        var fileInput = $(this);
        ImageValidator(fileInput);
    });

    function ImageValidator(fileInput) {
        if (fileInput.length && fileInput[0].files && fileInput[0].files.length) {
            var url = window.URL || window.webkitURL;
            var image = new Image();
            image.src = url.createObjectURL(fileInput[0].files[0]);
            console.log(fileInput[0].files[0].size);
            image.onload = function() {
                if($('#art-upload-img2').attr('src')){
                $('#art-upload-img2').cropper('destroy');
                }
                $('#art-upload-img').attr('src', image.src);
                $('#art-upload-img3').attr('src', image.src);
                $('#art-upload-img2').attr('src', image.src);
                $('#tab-1-Next').show();
            };
            image.onerror = function() {
                $(".image-error").show().delay(2000).queue(function(n) {
                    $(this).hide(); n();
                });
            };
        }
    }

    $(".upload-pagination a").click(function(event) {
        event.preventDefault();
        var tab = $(this).data("acc_tab");
        $('ul.tabs-menu a[href="'+tab+'"]').trigger('click');
        $('body,html').animate({
            scrollTop : 400
        }, 0);
    });



    
    
    
    /*Validation Code*/

    $('.tab3-bx1, .tab3-bx2, .tab3-bx3').click(function(){
        $("#tabThreeValidation").addClass('validation-pagination');
        $("#tab-3-Next").removeAttr("data-acc_tab","#tab-4");
        $("#tab-3-Next").removeAttr("href","#");
        if( $('input[name=is_copyright]').val() != '' &&  $('input[name=is_original]').val() != '' &&  $('#year').val() != '')
        {
            $("#tabThreeValidation").removeClass('validation-pagination');
            $("#tab-3-Next").attr("data-acc_tab","#tab-4");
            $("#tab-3-Next").attr("href","#");
        }

    });

    $('#art-description, #artname, #mykeywordDiv,.token-bx').keydown(function() {
        if($('#artname').val() != "" &&  $('#my-input').val() != "" &&
            $('#my-input2').val() != "" &&   $('#my-input3').val() != "" &&
            $('#my-keyword').val() != "" &&   $('#art-description').val() != "" )
        {
            $("#tabFiveValidation").removeClass('validation-pagination');
         //   $("#tab-5-Upload").attr("onClick","uploadloadArtwork()");
           // $("#tab-5-Next").attr("href","#");
        }
        else {
            $("#tabFiveValidation").addClass('validation-pagination');
        }
});
    $('#art-description, #artname, #mykeywordDiv,.token-bx').keyup(function() {
        if($('#artname').val() != "" &&  $('#my-input').val() != "" &&
            $('#my-input2').val() != "" &&   $('#my-input3').val() != "" &&
            $('#my-keyword').val() != "" &&   $('#art-description').val() != "" )
        {
            $("#tabFiveValidation").removeClass('validation-pagination');
          }
        else {
            $("#tabFiveValidation").addClass('validation-pagination');
        }
    });
    $('#art-description,.token-bx').change(function() {
        if($('#artname').val() != "" &&  $('#my-input').val() != "" &&
            $('#my-input2').val() != "" &&   $('#my-input3').val() != "" &&
            $('#my-keyword').val() != "" &&   $('#art-description').val() != "" )
        {
            $("#tabFiveValidation").removeClass('validation-pagination');
        }
        else {
            $("#tabFiveValidation").addClass('validation-pagination');
        }
    });

    

    $('#artname').keydown(function(){
        if($(this).val().length==30){
            $('#art-input-error').html('<div class="alert-danger col-sm-4"> ' +
                '<strong>Warning ! </strong> Max Character 30</div>');
        }else{
            $('#art-input-error').html('');
        }
    });

    $('#myinputDiv').keydown(function(){

        if($('#my-input').val().split(',').length == 5){
            $('#my-input-error').html('<div class="alert-danger col-sm-4"> ' +
                'Max tags 5</div>');
        }else{
            $('#my-input-error').html('');
        }
    });
    $('#myinput2Div').keydown(function(){

        if($('#my-input').val().split(',').length == 5){
            $('#my-input2-error').html('<div class="alert-danger col-sm-4"> ' +
                'Max tags 5</div>');
        }else{
            $('#my-input2-error').html('');
        }
    });
    $('#myinput3Div').keydown(function(){

        if($('#my-input3').val().split(',').length == 5){
            $('#my-input3-error').html('<div class="alert-danger col-sm-4"> ' +
                'Max tags 5</div>');
        }else{
            $('#my-input3-error').html('');
        }
    });

    $('#mykeywordDiv').keydown(function(){

        if($('#my-keyword').val().split(',').length == 12){
            $('#my-keyword-error').html('<div class="alert-danger col-sm-4"> ' +
                'Max keyword tags 12</div>');
        }else{
            $('#my-keyword-error').html('');
        }
    });



});




