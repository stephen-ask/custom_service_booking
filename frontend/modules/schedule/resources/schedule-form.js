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
	var memberid = '';
	
	/*Function when click on provider tab*/
	jQuery("#membertab li").click(function(){
										   
	jQuery('.loading-area').show();
	
	jQuery("#membertab li").removeClass('active');
	
	jQuery(this).addClass('active');
	
	memberid = jQuery(this).attr('data-staff-id'); //Get Member ID
	/*Prepare for Re-Initiaze Booking Calendar*/
	jQuery("#calendar").html('');
	jQuery('.loading-area').show();
	

	var getdate = jQuery('#calmonth').html();
	var res = getdate.split(" "); 
	var getmonth = res[0];
	var getyear = res[1];
	
	var monthnum = service_finder_convertMonthNameToNumber(getmonth);

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
		events_source: scheduleurl+'?member='+memberid+'&user_id='+user_id,
		view: 'month',
		language: 'bg-BG',
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
	
	function service_finder_convertMonthNameToNumber(monthName) {
    var myDate = new Date(monthName + " 1, 2000");
    var monthDigit = myDate.getMonth();
    return isNaN(monthDigit) ? 0 : (monthDigit + 1);
}
	
  });
  
  