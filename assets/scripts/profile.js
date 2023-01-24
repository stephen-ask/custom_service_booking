/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
// When the browser is ready...
jQuery(function() {
	'use strict';
	if(rtlnavoption == 'no'){
		var rtloption = false;
	 }else{
		var rtloption = true; 
	 }
	 
	if(relatedpronavoption == 'yes'){
		var relatedpronav = true;
	}else{
		var relatedpronav = false;
	}
	
	if(totalvideosoption == 'yes'){
		var totalvideosnav = true;
	}else{
		var totalvideosnav = false;
	}
	
	/*Style 4 favorite*/
	/*Add to Favorite*/
	jQuery('body').on('click', '.addtofavorite4', function(){

				var providerid = jQuery(this).attr('data-proid');
				var userid = jQuery(this).attr('data-userid');
				var data = {
						  "action": "addtofavorite",
						  "userid": userid,
						  "providerid": providerid
						};
						
				var formdata = jQuery.param(data);
				
				jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},
						
						data: formdata,
						
						dataType: "json",

						success:function (data, textStatus) {
							
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
								jQuery( '<a href="javascript:;" id="favproid-'+providerid+'" class="btn btn-primary removefromfavorite4" data-proid="'+providerid+'" data-userid="'+userid+'"><i class="fa fa-heart"></i> '+param.my_fav+'</a>' ).insertBefore( "#favproid-"+providerid );
								
								jQuery('#favproid-'+providerid+'.addtofavorite4').remove();

							}

							
						}

					});																
	});
	
	/*Remove from Favorite*/
	jQuery('body').on('click', '.removefromfavorite4', function(){

				var providerid = jQuery(this).attr('data-proid');
				var userid = jQuery(this).attr('data-userid');
				var data = {
						  "action": "removefromfavorite",
						  "userid": userid,
						  "providerid": providerid
						};
						
				var formdata = jQuery.param(data);
				
				jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},
						
						data: formdata,
						
						dataType: "json",

						success:function (data, textStatus) {
							
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
								jQuery( '<a href="javascript:;" id="favproid-'+providerid+'" class="btn btn-primary addtofavorite4" data-proid="'+providerid+'" data-userid="'+userid+'"><i class="fa fa-heart-o"></i> '+param.add_to_favorite+'</a>' ).insertBefore( "#favproid-"+providerid );
								
								jQuery('#favproid-'+providerid+'.removefromfavorite4').remove();

							}

							
						}

					});																
	});
	/*Style 4 favorite end*/
	
	jQuery('.sf-qes-answer-list li .sf-qestion-line').on('click',function(ev) {

		jQuery(this).parent().siblings("li").children(".sf-answer-line").slideUp(500, function(){
			jQuery(this).parent().removeClass('nav-active');
		});

		jQuery(this).next(jQuery('.sf-answer-line')).slideToggle(500, function(){
			jQuery(this).parent().toggleClass('nav-active');
		});

		ev.stopPropagation();
	});
	
	jQuery('.sf-video-carousel').owlCarousel({
        rtl: rtloption,
		loop:true,
		margin:30,
		dots: false,
		nav:totalvideosnav,
		navText: ['<span class="ar-left"></span>', '<span class="ar-right"></span>'],
		responsive:{
			0:{
				items:1,
			},
			480:{
				items:2
			},			
			767:{
				items:3
			}
		}
	});
	
	read_more_text();
	
	function read_more_text()
	{
		var maxLength = 90;
		jQuery(".show-read-more").each(function(){
			var reviewid = jQuery(this).data('reviewid');
			var myStr = jQuery(this).html();
			var myStr = myStr.replace("<p>", "");
			var myStr = myStr.replace("</p>", "");
		
			if(jQuery.trim(myStr).length > maxLength){
				var newStr = myStr.substring(0, maxLength);
				var removedStr = myStr.substring(maxLength, (jQuery.trim(myStr).length + 50));
				jQuery(this).empty().html(newStr);
				jQuery(this).append('<span class="sf-review-red-less read-more" id="readmore'+reviewid+'" data-reviewid="'+reviewid+'"> '+param.readmore+'</span>');
				jQuery(this).append('<span class="more-text" id="moretext'+reviewid+'"> ' + removedStr + '</span>');
			}
		});
	}
	
	jQuery("body").on("click", ".read-more", function(e){
		var reviewid = jQuery(this).data('reviewid');
		jQuery("#moretext"+reviewid).append('<span class="sf-review-red-less read-less" id="readless'+reviewid+'" data-reviewid="'+reviewid+'"> '+param.readless+'</span>');
		jQuery("#moretext"+reviewid).contents().unwrap();
		jQuery(this).remove();
	});
	
	jQuery("body").on('click', ".read-less", function(e){
		var reviewid = jQuery(this).data('reviewid');
		jQuery(this).remove();
		read_less_text(reviewid);
	});
	
	function read_less_text(reviewid = 0)
	{
		var maxLength = 90;
			var myStr = jQuery('#showreadmore'+reviewid).html();
			var myStr = myStr.replace("<p>", "");
			var myStr = myStr.replace("</p>", "");
		
			if(jQuery.trim(myStr).length > maxLength){
				var newStr = myStr.substring(0, maxLength);
				var removedStr = myStr.substring(maxLength, (jQuery.trim(myStr).length + 50));
				jQuery('#showreadmore'+reviewid).empty().html(newStr);
				jQuery('#showreadmore'+reviewid).append('<span class="sf-review-red-less read-more" id="readmore'+reviewid+'" data-reviewid="'+reviewid+'"> '+param.readmore+'</span>');
				jQuery('#showreadmore'+reviewid).append('<span class="more-text" id="moretext'+reviewid+'"> ' + removedStr + '</span>');
			}
	}
	
	// magnificPopup for video function=============================== //  
	if(jQuery('.mfp-link').length){
	jQuery('.mfp-link').magnificPopup({
		type: 'iframe',
	}); 
	}
	
	if(jQuery('.elem').length){
	lightbox_popup();
	// magnificPopup function
	function lightbox_popup(){
        lc_lightbox('.elem', {
            wrap_class: 'lcl_fade_oc',
            gallery : true,	
            thumb_attr: 'data-lcl-thumb', 
            
            skin: 'minimal',
            radius: 0,
            padding	: 0,
            border_w: 0,
        });
	}	
	}
	
	jQuery('.sf-ow-provi-related').owlCarousel({
        rtl: rtloption,
		loop:true,
		margin:20,
		nav:relatedpronav,
		dots: false,
		navText: ['<span class="ar-left"></span>', '<span class="ar-right"></span>'],
		responsive:{
			0:{
				items:1
			},
			600:{
				items:2
			},			
			1000:{
				items:2
			},
			1200:{
				items:3
			}
		}
	});
	
	jQuery('.sf-ow-provider-sidebar').owlCarousel({
        rtl: rtloption,
		loop:true,
		margin:0,
        items:1,
		nav:true,
		dots: false,
		navText: ['<span class="ar-left"></span>', '<span class="ar-right"></span>'],
	});
	
	// Magnific Popup by  = jquery.magnific-popup.js ================= // 	
	
	jQuery('.popup-gallery').magnificPopup({
	  delegate: 'a',
	  type: 'image',
	  tLoading: 'Loading image #%curr%...',
	  mainClass: 'mfp-img-mobile',
	  gallery: {
		enabled: true,
		navigateByImgClick: true,
		preload: [0,1] // Will preload 0 - before current, and 1 after the current image
	  },
	  image: {
		tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
		titleSrc: function(item) {
		  return item.el.attr('title') + '<small></small>';
		}
	  }
	});
	
	jQuery( 'body' ).on( 'click', '.sfviewallgallery', function (event)
	{
		jQuery('.mfp-gallery').magnificPopup({
          delegate: '.mfp-link2',
          type: 'image',
          tLoading: 'Loading image #%curr%...',
          mainClass: 'mfp-img-mobile',
          gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
          },
          image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: 'title'
          }
        }).magnificPopup('open');
	});
	
	mfp_gallery();
	// magnificPopup function
	function mfp_gallery(){
        jQuery('.mfp-gallery').magnificPopup({
          delegate: '.mfp-link2',
          type: 'image',
          tLoading: 'Loading image #%curr%...',
          mainClass: 'mfp-img-mobile',
          gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
          },
          image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: 'title'
          }
        });
	}
	
	jQuery('.sf-video-slider').owlCarousel({
		rtl: rtloption,
		loop:true,
		autoplay:true,
		margin:30,
		nav:true,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		onInitialize: function (event) {
			if (jQuery('.sf-video-slider .item').length <= 1) {
			   this.settings.loop = false;
			}
		},
		responsive:{
			0:{
				items:1
			},
			480:{
				items:2			
			},				
			991:{
				items:3
			}
		}
	});
	
	mfp_video_gallery();
	// magnificPopup for video function
	function mfp_video_gallery(){	
    jQuery('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });
	}
	
});