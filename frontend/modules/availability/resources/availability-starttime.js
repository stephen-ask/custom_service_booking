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
	var dayname = 'Mon';
	var tabname = 'monday';
	var mflag = 0;
	var tflag = 0;
	var wflag = 0;
	var thflag = 0;
	var frflag = 0;
	var saflag = 0;
	var suflag = 0;
	var toval;
	var startval;
	
	var monarr = [];
	var tuearr = [];
	var wedarr = [];
	var thurarr = [];
	var friarr = [];
	var satarr = [];
	var sunarr = [];
	
	var monbookarr = [];
	var tuebookarr = [];
	var wedbookarr = [];
	var thubookarr = [];
	var fribookarr = [];
	var satbookarr = [];
	var sunbookarr = [];
	
	var x = [];
	
	
	var maxbooking = '';
	var resMessageSlots = '';
	var resMessageIds = '';
	
	//Add Start times
    jQuery('.bulk-intervals')
	.bootstrapValidator({
		message: param.not_valid,
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			starttime: {
				validators: {
					notEmpty: {
						message: param.req
					}
				}
			},
			endtime: {
				validators: {
					notEmpty: {
						message: param.req
					}
				}
			},
			slotinterval: {
				validators: {
					notEmpty: {
						message: param.req
					}
				}
			},
			maxbooking: {
				validators: {
					notEmpty: {
						message: param.req
					}
				}
			},
		}
	})
	.on('success.form.bv', function(form) {
		jQuery('form.bulk-intervals').find('button[type="submit"]').prop('disabled', false);											
		
		// Prevent form submission
		var stimeval = jQuery('.bulk-intervals select[name="starttime"]').val();
		var etimeval = jQuery('.bulk-intervals select[name="endtime"]').val();
		var slotinterval = jQuery('.bulk-intervals select[name="slotinterval"]').val();
		var wdays = jQuery('.bulk-intervals input[name="weekdays[]"]:checked').val();
		
		if(stimeval == ''){
			jQuery(".alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_starttime+"</div>" ).insertBefore( "form.bulk-intervals" );
			return false;
		}
		
		if(etimeval == ''){
			jQuery(".alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_endtime+"</div>" ).insertBefore( "form.bulk-intervals" );
			return false;
		}
		
		var regex = new RegExp(':', 'g');
		if(parseInt(stimeval.replace(regex, ''), 10) < parseInt(etimeval.replace(regex, ''), 10)){
		} else {
			jQuery(".alert-danger").remove();

			jQuery( "<div class='alert alert-danger'>"+param.start_endtime_balance+"</div>" ).insertBefore( "form.bulk-intervals" );

			return false;
		}
		
		if(slotinterval == ''){
			jQuery(".alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_interval+"</div>" ).insertBefore( "form.bulk-intervals" );
			return false;
		}
		
		if(wdays == undefined){
			jQuery(".alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_weekday+"</div>" ).insertBefore( "form.bulk-intervals" );
			return false;
		}
		
		form.preventDefault();

		// Get the form instance
		var $form = jQuery(form.target);
		// Get the BootstrapValidator instance
		var bv = $form.data('bootstrapValidator');
		
		bootbox.confirm(param.bulk_slots_warning, function(result) {
			 if(result){
				var data = {
				  "action": "add_bulk_slots",
				  "user_id": user_id
				};
				
				var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
				
				jQuery.ajax({
		
							type: 'POST',
		
							url: ajaxurl,
							
							dataType: "json",
							
							beforeSend: function() {
								jQuery(".alert-success").remove();
								jQuery(".alert-danger").remove();
								jQuery('.loading-area').show();
							},
							
							data: formdata,
		
							success:function (data, textStatus) {
								jQuery('.loading-area').hide();
								$form.find('button[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
									jQuery('.sf-select-box').selectpicker('refresh');
									jQuery('input[name="weekdays[]"]').attr('checked', false);
									jQuery('form.allstarttime').bootstrapValidator('resetForm',true);
									jQuery('.sf-select-box').selectpicker('refresh');
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.bulk-intervals" );
									window.setTimeout(function(){
										window.location.href = data['redirect_url'];
									}, 2000);
								}else if(data['status'] == 'error'){
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.bulk-intervals" );
								}
								
								
							
							}
		
						});
			 }											 
		});
		
		
		
	});
	
	jQuery('body').on('click', 'a.reloadstarttimes', function(){
		var currentpage= jQuery(this).data('currentpage');
		window.location = currentpage;
	});
	
	//Add Start times
    jQuery('.allstarttime')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				starttime: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				maxbooking: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
            }
        })
        .on('success.form.bv', function(form) {
			jQuery('form.allstarttime').find('button[type="submit"]').prop('disabled', false);											
			
			// Prevent form submission
			var stimeval = jQuery('select[name="starttime"]').val();
			var wdays = jQuery('input[name="weekdays[]"]:checked').val();
			
			if(stimeval == ''){
				jQuery(".alert-danger").remove();
				jQuery( "<div class='alert alert-danger'>"+param.select_starttime+"</div>" ).insertBefore( "form.allstarttime" );
				return false;
			}
			
			if(wdays == undefined){
				jQuery(".alert-danger").remove();
				jQuery( "<div class='alert alert-danger'>"+param.select_weekday+"</div>" ).insertBefore( "form.allstarttime" );
				return false;
			}
			
            form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "add_start_time",
			  "user_id": user_id
			};
			
			var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery(".alert-success").remove();
							jQuery(".alert-danger").remove();
							jQuery('.loading-area').show();
						},
						
						data: formdata,

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							$form.find('button[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								jQuery('.sf-select-box').selectpicker('refresh');
								jQuery('input[name="weekdays[]"]').attr('checked', false);
								jQuery('form.allstarttime').bootstrapValidator('resetForm',true);
								jQuery('.sf-select-box').selectpicker('refresh');
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.allstarttime" );
								window.setTimeout(function(){
									jQuery(".alert-success").remove();
									jQuery(".alert-danger").remove();
								}, 2000);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.allstarttime" );
							}
							
							
						
						}

					});
			
    });
	
	
	/*Click event on subtabs*/
	jQuery('body').on('click', '#subTab li a', function(){
		dayname = jQuery(this).attr('href');													
		dayname = dayname.replace("#", "");
		tabname = dayname;
		dayname = dayname.replace("day", "");
		dayname = dayname.substr(0, 1).toUpperCase() + dayname.substr(1);
	});	
	
	/*Remove starttime*/
	jQuery('body').on('click', '.removestarttime', function(){
		
		var $this = jQuery(this).parents('li');
		var tid = jQuery(this).parents('li').data('tid');
		
		bootbox.confirm(param.are_you_sure, function(result) {
			 if(result){
				var data = {
				  "action": "remove_start_time",
				  "tid": tid,
				  "user_id": user_id,
				};
				
				var data = jQuery.param(data);
				
				jQuery.ajax({
	
							type: 'POST',
	
							url: ajaxurl,
							
							data: data,
							
							beforeSend: function() {
								jQuery('.loading-area').show();
							},
	
							success:function (data, textStatus) {
								jQuery('.loading-area').hide();
								$this.remove();
							}
	
						});		 
			 }											 
		});
		
	});
	
	/*Update starttime max booking*/
	jQuery('body').on('click', '.updatemaxbooking', function(){
		
		var $this = jQuery(this).parents('li');
		var tid = jQuery(this).parents('li').data('tid');
		var maxbooking = jQuery('#maxbooking_'+tid).val();

		bootbox.confirm(param.are_you_sure, function(result) {
			 if(result){
				var data = {
				  "action": "upadte_maxbooking",
				  "tid": tid,
				  "maxbooking": maxbooking,
				  "user_id": user_id,
				};
				
				var data = jQuery.param(data);
				
				jQuery.ajax({
	
							type: 'POST',
	
							url: ajaxurl,
							
							data: data,
							
							beforeSend: function() {
								jQuery('.loading-area').show();
							},
	
							success:function (data, textStatus) {
								jQuery('.loading-area').hide();
								if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( '.selected-time' );
								window.setTimeout(function(){
									jQuery(".alert-success").remove();
									jQuery(".alert-danger").remove();
								}, 2000);
								}else if(data['status'] == 'error'){
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( '.selected-time' );
								}
							}
	
						});		 
			 }											 
		});
		
	});
	
  });
  
