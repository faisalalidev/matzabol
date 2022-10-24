/*
 * flogin.js
 * stupidly simple facebook login as a jQuery plugin
 *
 * Gary Rafferty - 2013
 */
(function ($) {
    $.fn.facebook_login = function (options) {
        var defaults = {
            appId: '148761889056719',
            endpoint: '/sessions/new',
            permissions: 'email',
            onSuccess: function (data) {
                console.log([200, 'OK']);
            },
            onError: function (data) {
                console.log([500, 'Error']);
            }
        };

        var settings = $.extend({}, defaults, options);

        if (settings.appId === 'undefined') {
            console.log('You must set the appId');
            return false;
        }

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "http://connect.facebook.net/en_US/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        window.fbAsyncInit = function () {
            FB.init({
                appId: settings.appId,
                status: true,
                xfbml: true
            });
        };

        this.bind('click', function () {
            FB.login(function (r) {
                var response;
                if (response = r.authResponse) {
                    var user_id = response.userID;
                    var token = response.accessToken;

                    FB.api('/me?access_token=' + token, {fields: 'first_name,last_name,email'}, function (user) {

/*
* code 200 = profile
* code 201 = user not found ... route to registration page
* code 300 = user registered as buyer
* code 301 = user registered as seller
* code 404 = user already registered -
* */
                        $.ajax({
                            url: settings.endpoint,
                            data: {
                                facebook_id: user_id,
                                token: token,
                                email: user.email,
                                name: user.first_name,
                                lastname: user.last_name
                            },
                            type: 'POST',
                            async: false,
                        }).done(function (res) {
                            //console.log(res);
                            if (res.code == 200) {
                                window.location.href =  '/profile';
                            }
                            if(res.code == 404){
                                //alert(res.messageContent);
                                window.location.href =  '/profile';
                            }
                            if(res.code == 201){
                                localStorage.setItem("email",res.records.email);
                                localStorage.setItem("name",res.records.name);
                                localStorage.setItem("lastname",res.records.lastname);
                                localStorage.setItem("facebook_id",res.records.facebook_id);
                                window.location.href =  '/form';

                              }
                            if(res.code == 300){
                                $('#BuyerEmail').val(res.records.email);
                                $('#buyerName').val(res.records.name);
                                $('#buyerLastname').val(res.records.lastname);
                                $('#buyerFacebook_id').val(res.records.facebook_id);
                                /*  $('form#fb_form').submit();*/
                                if ($('#buyerFacebook_id').val() != ""){
                                    $("#buyerDivPass,#buyerDivPass1").hide();
                                }else {
                                    $("#buyerDivPass,#buyerDivPass1").show();
                                }
                                $("#BuyerEmail,#buyerName,#buyerLastname").prop("readonly", true);

                            }
                            if(res.code == 301){
                                $('#sellerEmail').val(res.records.email);
                                $('#sellerName').val(res.records.name);
                                $('#sellerLastname').val(res.records.lastname);
                                $('#sellerFacebook_id').val(res.records.facebook_id);
                             /*   $('form#fb_form').submit();*/
                                if ($('#sellerFacebook_id').val() != ""){
                                    $("#sellerDivpass2, #sellerDivpass1").hide();
                                }else {
                                    $("#sellerDivpass1,#sellerDivpass2").show();
                                }
                                $("#sellerEmail,#sellerName,#sellerLastname").prop("readonly", true);
                            }

                        });
                    });
                } else {
                    settings.onError();
                }
            }, {scope: settings.permissions});

            return false;
        });
    }
})(jQuery);

