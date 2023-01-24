(function(jQuery) {
	"use strict";
	var provider = 'all';

	jQuery('.loading-area').show();
	var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!

    var yyyy = today.getFullYear();
    if(dd<10){
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 
    var today = yyyy+'-'+mm+'-'+dd;
	var options = {
		events_source: adminscheduleurl+'?provider='+provider,
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
			jQuery('.loading-area').hide();
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

	var calendar = jQuery('#calendar').calendar(options);

	jQuery('.btn-group button[data-calendar-nav]').each(function() {
		var $this = jQuery(this);
		$this.click(function() {
			provider = getMemberid();
			calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
			calendar.navigate($this.data('calendar-nav'));
		});
	});

	jQuery('.btn-group button[data-calendar-view]').each(function() {
		var $this = jQuery(this);
		$this.click(function() {
			provider = getMemberid();
			calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
			calendar.view($this.data('calendar-view'));
		});
	});

	jQuery('#first_day').change(function(){
		var value = jQuery(this).val();
		provider = getMemberid();
		calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
		value = value.length ? parseInt(value) : null;
		calendar.setOptions({first_day: value});
		calendar.view();
	});

	jQuery('#language').change(function(){
		provider = getMemberid();
		calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
		calendar.setLanguage(jQuery(this).val());
		calendar.view();
	});

	jQuery('#events-in-modal').change(function(){
		provider = getMemberid();
		calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
		var val = jQuery(this).is(':checked') ? jQuery(this).val() : null;
		calendar.setOptions({modal: val});
	});
	jQuery('#format-12-hours').change(function(){
		provider = getMemberid();
		calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
		var val = jQuery(this).is(':checked') ? true : false;
		calendar.setOptions({format12: val});
		calendar.view();
	});
	jQuery('#show_wbn').change(function(){
		provider = getMemberid();				
		calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
		var val = jQuery(this).is(':checked') ? true : false;
		calendar.setOptions({display_week_numbers: val});
		calendar.view();
	});
	jQuery('#show_wb').change(function(){
		provider = getMemberid();
		calendar.setOptions({events_source: adminscheduleurl+'?provider='+provider});
		var val = jQuery(this).is(':checked') ? true : false;
		calendar.setOptions({weekbox: val});
		calendar.view();
	});
	
	jQuery('#events-modal .modal-header, #events-modal .modal-footer').click(function(e){
	});
	
	function getMemberid(){
		provider = jQuery("#providertab li.active").data('staff-id');
		if(provider > 0){
		return provider;	
		}else{
		return 'all';	
		}
		
	}
}(jQuery));