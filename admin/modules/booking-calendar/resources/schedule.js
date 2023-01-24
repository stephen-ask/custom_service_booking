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
	
	var providerid = '';
	
	/*Function when click on provider tab*/
	jQuery('ul#alphabetsort').on('click', 'li', function(){
	var char = jQuery(this).attr('data-char'); //Get Charecter
	
	jQuery('ul#alphabetsort li').removeClass('active');
	jQuery(this).addClass('active');
	
	var data = {
				  "action": "get_providers_list",
				  "char": char
				};
								
	var formdata = jQuery.param(data);
	
	jQuery.ajax({

				type: 'POST',

				url: ajaxurl,
				
				beforeSend: function() {
					jQuery('.loading-area').show();
				},
				
				data: formdata,

				success:function (data, textStatus) {
					jQuery('.loading-area').hide();
					jQuery("#providertab").html(data);
				}

			});
								
	});
	
	/*Function when click on provider tab*/
	jQuery('ul#providertab').on('click', 'li', function(){
	jQuery('.loading-area').show();
	
	jQuery("#providertab li").removeClass('active');
	
	jQuery(this).addClass('active');
	
	providerid = jQuery(this).attr('data-staff-id'); //Get Member ID
	/*Prepare for Re-Initiaze Booking Calendar*/
	jQuery("#calendar").html('');
	jQuery('.loading-area').show();
	

	var getdate = jQuery('#calmonth').html();
	var res = getdate.split(" "); 
	var getmonth = res[0];
	var getyear = res[1];
	
	var monthnum = convertMonthNameToNumber(getmonth);

	var today = new Date();
    var dd = today.getDate();
    var mm = monthnum; //January is 0!

    var yyyy = getyear;
    if(dd<10){
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 
    var today = yyyy+'-'+mm+'-'+dd;
	
	var options = {
		events_source: adminscheduleurl+'?provider='+providerid,
		view: 'month',
		language: param.lang,
		tmpl_path: caltmpls,
		tmpl_cache: false,
		day: today,
		modal: "#events-modal",
		modal_type : "ajax",
		modal_title : function (e) { 
		jQuery('.display-ratings').rating();
		jQuery('.sf-show-rating').show();
		},
		onAfterEventsLoad: function(events) {
			if(!events) {
				return;
			}
			var list = jQuery('#eventlist');
			list.html('');

			jQuery.each(events, function(key, val) {
				jQuery(document.createElement('li'))
					.html('<a href="' + val.url + '">' + val.title + '</a>')
					.appendTo(list);
			});
		},
		onAfterViewLoad: function(view) {
			jQuery('#calmonth').text(this.getTitle());
			jQuery('.btn-group button').removeClass('active');
			jQuery('button[data-calendar-view="' + view + '"]').addClass('active');
		},
		classes: {
			months: {
				general: 'label'
			}
		}
	};

	jQuery('.loading-area').hide();
	/*Re-Generate Booking Calendar*/
	var calendar = jQuery('#calendar').calendar(options);

	});
	
	jQuery('#events-modal .modal-header, #events-modal .modal-footer').click(function(e){
	});
	
	function convertMonthNameToNumber(monthName) {
    var myDate = new Date(monthName + " 1, 2000");
    var monthDigit = myDate.getMonth();
    return isNaN(monthDigit) ? 0 : (monthDigit + 1);
}
	
  });
  
  