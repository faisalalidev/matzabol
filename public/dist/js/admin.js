$(document).ready(function(e) {
	
	$('.select_all_ckbox').click(function(){
		if($(this).prop('checked'))
			$('.bulk_delete').prop('checked',true);
		else
			$('.bulk_delete').removeAttr('checked');
	});
	
	$('#delete_rows').click(function(){
		var delete_rows_str = $(".bulk_delete:checked").map(function(){
		   return $(this).val();
		}).get().join("^");
		var href	=	$(this).data('href');
		if(delete_rows_str){
			BootstrapDialog.show({
				message: 'Are you sure you want to delete?',
				closable: false,
				buttons: [
				{
					label: 'OK',
					cssClass: 'btn-primary',
					action: function(){
					 window.location	=	href+delete_rows_str;
					}
				},
				{
					label: 'Close',
					action: function(dialogItself){
						dialogItself.close();
					}
				}]
			});	
		}
		else{
			BootstrapDialog.show({
				message: 'Please select atleast 1 row!',
				closable: true
			});	
		}
		
	});
	
    $('.colorpicker').colorpicker();
	$('[data-toggle="tooltip"],[ref="tooltip"]').tooltip(); 
    //$('#side-menu').metisMenu();
	$('.datetimepicker').datetimepicker();
	$('.datepicker').datetimepicker({
		format:'YYYY-MM-DD'
	});
	$('.timepicker').datetimepicker({
		format: 'H:s'
     });
	 
	$('.summernote').summernote({
	  height:150,
	  onImageUpload: function(files, editor, welEditable) {
              	sendFile(files[0], editor, welEditable);
      }
	});
	
	
	
	$('body').on('click','.cus_status',function(e){

		var ele	=	$(this);
		e.preventDefault();
		
		
		BootstrapDialog.show({
            message: ele.data('msg'),
			closable: false,
            buttons: [
			{
                label: 'OK',
                cssClass: 'btn-primary',
                action: function(){
                 window.location = ele.attr('href');
            	}
            },
			{
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
	
	});
	
	$('.thumbnail').hover(
        function(){
            $(this).find('.caption').slideDown(250); //.fadeIn(250)
        },
        function(){
            $(this).find('.caption').slideUp(250); //.fadeOut(205)
        }
    ); 
	
	$('body').on('click','.getAlbum',function(){
		var album_id	=	$(this).data('id');
		var album_name	=	$(this).data('album_name');
		$('.album-name').text(album_name);
		
		$.ajax({
			url:api_url+'get-all-gallery-by-album-id/',
			data:'album_id='+album_id,
			type:'POST',
			context: $(this),
			 beforeSend: function (  ) {
				$("#galleryModal").modal();
				$('.gallery-data').text('Processing...');
			},
			success:function(res){
				if(res.code == 200){
					var html = '';
					var cssClass = '';
					var action = '';
					$.each(res.records,function(ind,val){
						var cssClass = (val.status == 'yes')? 'primary': 'default';
						var action = (val.status == "yes")? 'Active' : 'Deactive';
						html += '<div class="col-md-3"> <div class="thumbnail"><div class="caption"> <h4>'+album_name+'</h4> <p> <a  class="label label-danger deleteGalleryStatus" data-id="'+val.id+'" ref="tooltip" title="Delete">Delete</a> <a  class="label label-'+cssClass+' updateGalleryStatus" ref="tooltip" title="'+action+'" data-id="'+val.id+'" data-album_name="">'+action+'</a> </p> </div> <img src="'+val.image+'" alt="..."></div></div>'
					});
				}
				else{
					html = '<div class="col-md-3">No record found...</div>';
				}
				
				$('.gallery-data').html(html);
				
				$('[data-toggle="tooltip"],[ref="tooltip"]').tooltip();
				$('.thumbnail').hover(
					function(){
						$(this).find('.caption').slideDown(250); //.fadeIn(250)
					},
					function(){
						$(this).find('.caption').slideUp(250); //.fadeOut(205)
					}
				); 
			},
			error:function(res){
				BootstrapDialog.show({
					message: res.statusText,
					closable: false,
					buttons: [
					{
						label: 'Close',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});
			}
		});
		
	
	});
	
	$('body').on('click','.getUsersAddedImages',function(){
		var album_id	=	$(this).data('id');
		var album_name	=	$(this).data('album_name');
		$('.album-name').text(album_name);
		
		$.ajax({
			url:api_url+'get-users-gallery-by-album-id/',
			data:'album_id='+album_id,
			type:'POST',
			context: $(this),
			 beforeSend: function (  ) {
				$("#galleryModal").modal();
				$('.gallery-data').text('Processing...');
			},
			success:function(res){
				if(res.code == 200){
					var html = '';
					var cssClass = '';
					var action = '';
					$.each(res.records,function(ind,val){
						var cssClass = (val.status == 'yes')? 'primary': 'default';
						var action = (val.status == "yes")? 'Active' : 'Deactive';
						html += '<div class="col-md-3"> <div class="thumbnail"><div class="caption"> <h4>'+album_name+'</h4> <p> <a  class="label label-danger deleteGalleryStatus" data-id="'+val.id+'" ref="tooltip" title="Delete">Delete</a> <a  class="label label-'+cssClass+' updateGalleryStatus" ref="tooltip" title="'+action+'" data-id="'+val.id+'" data-album_name="">'+action+'</a> </p> </div> <img src="'+val.image+'" alt="..."></div></div>'
					});
				}
				else{
					html = '<div class="col-md-3">No record found...</div>';
				}
				
				$('.gallery-data').html(html);
				
				$('[data-toggle="tooltip"],[ref="tooltip"]').tooltip();
				$('.thumbnail').hover(
					function(){
						$(this).find('.caption').slideDown(250); //.fadeIn(250)
					},
					function(){
						$(this).find('.caption').slideUp(250); //.fadeOut(205)
					}
				); 
			},
			error:function(res){
				BootstrapDialog.show({
					message: res.statusText,
					closable: false,
					buttons: [
					{
						label: 'Close',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});
			}
		});
		
	
	});
	
	
	$('body').on('click','.updateGalleryStatus',function(){
		var text	=	$(this).text();
		var gallery_id	=	$(this).data('id');
		
		if(text == 'Active')
			var status = 'no';
		else
			var status = 'yes';
		
		
		$.ajax({
			url:api_url+'edit-gallery-status-by-id/',
			data:'id='+gallery_id+'&status='+status,
			type:'POST',
			context: $(this),
			 beforeSend: function (  ) {
				$(this).text('Wait...');
			},
			success:function(res){
				if(res.code == 200){
					if(text == 'Active'){
						$(this).removeClass('label-primary');
						$(this).addClass('label-default');
						//$(this).attr('title','Deactive');
						$(this).tooltip('hide').attr('title','Deactive').tooltip('fixTitle').tooltip('show');
						$(this).text('Deactive');
						
					}
					else{
						$(this).removeClass('label-default');
						$(this).addClass('label-primary');
						//$(this).attr('title','Active');
						$(this).tooltip('hide').attr('title','Active').tooltip('fixTitle').tooltip('show');
						$(this).text('Active');
						
					}	
				}
				
			},
			error:function(res){
				BootstrapDialog.show({
					message: res.statusText,
					closable: false,
					buttons: [
					{
						label: 'Close',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});
			}
		});
		
	
	});
	
	$('body').on('click','.deleteGalleryStatus',function(){
		var gallery_id	=	$(this).data('id');
		
		$.ajax({
			url:api_url+'delete-gallery-images-by-id/',
			data:'id='+gallery_id,
			type:'POST',
			context: $(this),
			 beforeSend: function (  ) {
				$(this).text('Wait...');
			},
			success:function(res){
				if(res.code == 200){
					
					var is_del = false;
				
					if($(this).closest('.gallery-data').children().length == 1)
						is_del = true;
					$(this).closest('.col-md-3').remove();
					if(is_del == true)
						$('.gallery-data').html('<div class="col-md-3">No record found...</div>');
					
				}
				
			},
			error:function(res){
				BootstrapDialog.show({
					message: res.statusText,
					closable: false,
					buttons: [
					{
						label: 'Close',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});
			}
		});
		
	
	});
	
	$('body').on('click','.deleteAlbum',function(){
		var album_id	=	$(this).data('id');
		
		$.ajax({
			url:api_url+'delete-album-by-id/',
			data:'id='+album_id,
			type:'POST',
			context: $(this),
			 beforeSend: function (  ) {
				$(this).text('Wait...');
			},
			success:function(res){
				if(res.code == 200){
					
					var is_del = false;
				
					if($(this).closest('.panel-body').children().length == 1)
						is_del = true;
					$(this).closest('.col-md-3').remove();
					if(is_del == true)
						$('.panel-body').html('<h4>No record found...</h4>');
				}
				
			},
			error:function(res){
				BootstrapDialog.show({
					message: res.statusText,
					closable: false,
					buttons: [
					{
						label: 'Close',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});
			}
		});
		
	
	});
	
	
	$('body').on('click','.notification-dropdown',function(){
		
		if(updateNotificationStatus == 'false'){
			$.ajax({
				url:api_url+'update-notification-status/',
				data:{},
				type:'POST',
				context: $(this),
				 beforeSend: function (  ) {
				},
				success:function(res){
					updateNotificationStatus = 'true';
					if(res.code == 200){
						$('.notification-counter').hide();		
					}
					
				},
				error:function(res){
					BootstrapDialog.show({
						message: res.statusText,
						closable: false,
						buttons: [
						{
							label: 'Close',
							action: function(dialogItself){
								dialogItself.close();
							}
						}]
					});
				}
			});
		}
		
	
	});
	
	
}); 

var waitingDialog = waitingDialog || (function ($) {
    'use strict';

	// Creating modal dialog's DOM
	var $dialog = $(
		'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
		'<div class="modal-dialog modal-m">' +
		'<div class="modal-content">' +
			'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
			'<div class="modal-body">' +
				'<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
			'</div>' +
		'</div></div></div>');

	return {
		/**
		 * Opens our dialog
		 * @param message Custom message
		 * @param options Custom options:
		 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
		 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
		 */
		show: function (message, options) {
			// Assigning defaults
			if (typeof options === 'undefined') {
				options = {};
			}
			if (typeof message === 'undefined') {
				message = 'Loading...';
			}
			var settings = $.extend({
				dialogSize: 'm',
				progressType: '',
				onHide: null // This callback runs after the dialog was hidden
			}, options);

			// Configuring dialog
			$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
			$dialog.find('.progress-bar').attr('class', 'progress-bar');
			if (settings.progressType) {
				$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
			}
			$dialog.find('h3').text(message);
			// Adding callbacks
			if (typeof settings.onHide === 'function') {
				$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
					settings.onHide.call($dialog);
				});
			}
			// Opening dialog
			$dialog.modal();
		},
		/**
		 * Closes dialog
		 */
		hide: function () {
			$dialog.modal('hide');
		}
	};

})(jQuery);

function sendFile(file, editor, welEditable) 
{
	data = new FormData();
	data.append("image", file);
	$.ajax({
		data: data,
		type: "POST",
		url: AdminUrl+'upload-editor-image',
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function (  ) {
			waitingDialog.show();
		},
		success: function(res) {
			waitingDialog.hide();
			
			if(res.status == 'success')
			{
				editor.insertImage(welEditable, UPLOADS_ACCESS_PATH+'summernote_images/'+res.records);
			}
			else{
				BootstrapDialog.show({
						message: res.messageContent,
						closable: false,
						buttons: [
						{
							label: 'Close',
							action: function(dialogItself){
								dialogItself.close();
							}
						}]
					});
			}
			
			
		}
	});
}

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});


// JavaScript Document

var server		= window.location.hostname;
var canvas_url    = "";
if (server=='localhost' || server=='khilt-661' || server =='cricroxen.com')
{
	canvas_url       = location.protocol+"//"+server+"/amcham/";
	AdminUrl		 = location.protocol+"//"+server+"/amcham/admin/";
	api_url		 	 = location.protocol+"//"+server+"/amcham/service/";
	IMAGES_PATH		 = location.protocol+"//"+server+"/amcham/assets/images/";
	UPLOADS_ACCESS_PATH		 = location.protocol+"//"+server+"/amcham/assets/uploads/";
}
else 
{
	canvas_url	= location.protocol+"//"+server+"/mobile_app";
	AdminUrl		= location.protocol+"//"+server+"/mobile_app/admin/";
	api_url		 	= location.protocol+"//"+server+"/mobile_app/service/";
	IMAGES_PATH	= location.protocol+"//"+server+"/mobile_app/assets/images/";
	UPLOADS_ACCESS_PATH	= location.protocol+"//"+server+"/mobile_app/assets/uploads/";
	
}

var API_URL    = canvas_url+"api/";

//var canvasPage = "http://developer.cygnismedia.com/hobbule/public/";
var canvasPage = canvas_url;

var cUrl 		= window.location;
var sPath 		= window.location.pathname;
var sPage 		= sPath.substring(sPath.lastIndexOf('/') + 1);   //if(sPage == 'index.php')

var applicationId     = "938307529530671";
var applicationSecret = "8d7a52da405a645ae60ab5a747fdf394";
var APP_URL	=	'http://apps.facebook.com/favPlate/';

var session_key;
var access_token;
var uid;
//var emailRegex	= /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
var emailRegex = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

var phoneRegex	= /^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/;
var numRegex	= /^\d+$/;
var urlRegex	= /(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/;

var language = 'en';
var userData = Array();

var facebook_id=0;
var access_token=0;
var API_URL = canvas_url+'api/';
var buddyArray	=	Array();
var checkedArray	=	Array();
var buddyDBArray	=	Array();
var venueLongiLati = Array();
var currLongiLati = Array();
var buddyArrayLocal = Array();
var buddyNameArrayLocal = Array();
var checkedArrayName = Array();
var country = "";
var state	= "";
var city	= "";

var seletedGearArray	=	Array();

var appInstallUrl = "https://www.facebook.com/dialog/oauth/?client_id="+applicationId+"&redirect_uri="+canvasPage+"&scope=email";

var FB_IMAGE_URL = location.protocol+'//graph.facebook.com/@userid@/picture?width=@width@&height=@height@';

var updateNotificationStatus = 'false';
