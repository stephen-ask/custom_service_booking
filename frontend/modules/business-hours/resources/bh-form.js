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
	var schedule_container;
	var $schedule_form;
	var breaktime_container;
	var break_start_time;
	var offdays = [];
	var currentbreakweekday;
	
	jQuery('#businesshours-active').change(function() {
	  var providerid = jQuery(this).data("providerid");														
      if(jQuery(this).prop('checked')){
		var status = 'active';
	  }else{
		var status = 'inactive';
	  }
	  var data = {
					  "action": "businesshours_active_or_inactive",
					  "status": status,
					  "providerid": providerid
				};
		var formdata = jQuery.param(data);
		
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: formdata,
				dataType : 'json',
				beforeSend: function() {
					jQuery(".alert-success,.alert-danger").remove();
					jQuery('.loading-area').show();
				},
				success:function (data, textStatus) {
					jQuery('.loading-area').hide();	
					jQuery( ".sf-cancel-membership" ).remove();
					jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.provider-bh-hour" );
				}
		});	
    })
	
	jQuery('body').on('click', '.businesshours-active', function(){
		var providerid = jQuery(this).data("providerid");
		
		bootbox.confirm(param.are_you_sure_reset_business_hours, function(result) {
		if(result){
			var data = {
						  "action": "reset_business_hours",
						  "providerid": providerid
					};
			var formdata = jQuery.param(data);
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: formdata,
					dataType : 'json',
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();	
						jQuery( ".sf-cancel-membership" ).remove();
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.provider-bh-hour" );
						if(data['redirect'] != ""){
						window.location.href= data['redirect'];
						}
					}
			});			
		}
		}); 
		
	});
	
	jQuery('.bh-onoff').change(function() {
      var onoffweekday = jQuery(this).data('weekday');
	  if(jQuery(this).prop('checked')){
		jQuery('#weekdaybx-'+onoffweekday+' .sf-bh-overlay').hide();
	  }else{
		jQuery('#weekdaybx-'+onoffweekday+' .sf-bh-overlay').show();
	  }
    })
	
	jQuery('body').on('click', '.sf-breakpopup-close', function(){
		jQuery('#addbreaktimepopup,.sf-couponcode-popup-overlay').fadeOut("slow");
	})
	
	jQuery('body').on('click', '.openbreaktimepopup', function(){
		jQuery('.alert').remove();
		
		currentbreakweekday = jQuery(this).data('weekday');
		jQuery('#addbreaktimepopup,.sf-couponcode-popup-overlay').fadeIn("slow");
		jQuery('.addbreaktimeform input[name="weekday"]').val(currentbreakweekday);
		load_added_breaktme(user_id,currentbreakweekday);
	})
	
	//load_breaktime_popup(user_id);
	
	schedule_container = jQuery('#pro-bh');
	$schedule_form = jQuery('form', schedule_container);
	
	jQuery('.working-start', schedule_container).each(function () {
		jQuery(this).data('default_value', jQuery(this).val());
	});

	jQuery('.working-end', schedule_container).each(function () {
		jQuery(this).data('default_value', jQuery(this).val());
	});

	// Resets initial values
	jQuery('#ab-schedule-reset').bind('click', function () {
		jQuery('.working-start', schedule_container).each(function () {
			jQuery(this).val(jQuery(this).data('default_value'));
			jQuery(this).trigger('change');
		});

		jQuery('.working-end', schedule_container).each(function () {
			jQuery(this).val(jQuery(this).data('default_value'));
		});
	});
						
	 // when the working day is disabled (working start time is set to "OFF")
	// hide all the elements inside the row
	schedule_container.find('.working-start').bind('change', function(){
		var $this = jQuery(this),
			$row  = $this.parents('.staff-schedule-item-row');

		if (!$this.val()) {
			$row.find('.hide-on-non-working-day').hide();
		} else {
			$row.find('.hide-on-non-working-day').show();
		}
	});
	jQuery('.working-start', schedule_container).on('change', function () {
		 var $row = jQuery(this).parents('.staff-schedule-item-row').first(),
			$end_select = jQuery('.working-end', $row),
			start_time = jQuery(this).val();
		jQuery('option', $end_select).each(function () {
			if ( start_time < jQuery(this).val()) {
				jQuery(this).show();
			}
		});

		// Hides end time options with value less than in the start time
		jQuery('option', $end_select).each(function () {
			if (jQuery(this).val() <= start_time) {
				jQuery(this).hide();
			}
		});
		jQuery('select').selectpicker('refresh');
	})//.trigger('change');
	
	jQuery('body').on('click', '#sf-bh-update', function(){
				var data = {};
				jQuery('select.working-start, select.working-end', $schedule_form).each(function() {
					data[this.name] = this.value;
				});

				var paramdata = {
				  "action": "update_business_hours",
				  "user_id": user_id,
				};
				
				var formdata = jQuery('.provider-bh-hour').serialize() + "&" + jQuery.param(data) + "&" + jQuery.param(paramdata);
				
				jQuery.ajax({

					type: 'POST',
			
					url: ajaxurl,
					
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					
					data: formdata,
					
					dataType: "json",
			
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.provider-bh-hour" );
					}
			
			
				});
			});
	
	function load_added_breaktme(user_id,weekday){
		
		
		var data = {
		"action": "get_bh_breaktime",
		"user_id": user_id,
		"weekday": weekday
		};
		
		var formdata = jQuery.param(data);
		
		jQuery.ajax({
		
		type: 'POST',
		
		url: ajaxurl,
		
		dataType: "json",
		
		beforeSend: function() {
			jQuery(".alert-success,.alert-danger").remove();
			jQuery('.loading-area').show();
		},
		
		data: formdata,
		
		success:function (data, textStatus) {
			jQuery('.loading-area').hide();
			filter_breaktime(user_id,weekday,data);
			//load_end_breaktime(user_id,weekday);
		},
		error:function (data, textStatus) {
			jQuery('.loading-area').hide();
		}
		
		});
		
	}
	
	function filter_breaktime(user_id,weekday,data){
		
		var getstarttime = jQuery('.provideravl-'+user_id+' select[name="start_time['+weekday+']"]').val();
		var getendtime = jQuery('.provideravl-'+user_id+' select[name="end_time['+weekday+']"]').val();
		
		breaktime_container = jQuery('.addbreaktimeform');
		var $break_end_select_f = jQuery('.break-end', breaktime_container);
		var $break_start_select_f = jQuery('.break-start', breaktime_container);
		
		// Set start time option between working hours
		jQuery('option', $break_start_select_f).each(function () {
			if (jQuery(this).val() < getstarttime || jQuery(this).val() > getendtime) {
				jQuery(this).hide();
			}else{
				var $this = jQuery(this);
				$this.show();
				
				if(data != null && data != ''){
				jQuery.each( data, function( index, value ){
					
					var breakslot = value.split("-");
					var breakstart = breakslot[0];
					var breakend = breakslot[1];
					
					if($this.val() >= breakstart && $this.val() <= breakend){
						$this.prop('disabled',true);
					}
				});
				}else{
					$this.prop('disabled',false);
				}
				
			}
		});
		
		// Set end time option between working hours
		jQuery('option', $break_end_select_f).each(function () {
			if (jQuery(this).val() < getstarttime || jQuery(this).val() > getendtime) {
				jQuery(this).hide();
			}else{
				var $this = jQuery(this);
				$this.show();
				
				if(data != null){
				jQuery.each( data, function( index, value ){
					var breakslot = value.split('-');
					var breakstart = breakslot[0];
					var breakend = breakslot[1];
					
					if($this.val() >= breakstart && $this.val() <= breakend){
						$this.prop('disabled',true);
					}
				});
				}else{
					$this.prop('disabled',false);	
				}
			}
		});
		jQuery($break_start_select_f).val(getstarttime);
		jQuery('select').selectpicker('refresh');
		
	}
	
	function load_end_breaktime(user_id,weekday){
		
		breaktime_container = jQuery('.addbreaktimeform');
		jQuery('.break-start', breaktime_container).on('change', function () {
				//var $row = jQuery(this).parents('.break-schedule-item-row').first(),
				var $break_end_select = jQuery('.break-end', breaktime_container),
				break_start_time = jQuery(this).val();
				
				
				var getendtime = jQuery('.provideravl-'+user_id+' select[name="end_time['+weekday+']"]').val();
				
				
				//var disabledprop = '';
				//var disabledflag = true;
				jQuery('option', $break_end_select).each(function () {
					if ( break_start_time < jQuery(this).val()) {
						//jQuery(this).unwrap();
						jQuery(this).show();
					}
					
					/*disabledprop = jQuery(this).attr('disabled');																   
					if(disabledprop == 'disabled' && jQuery(this).val() > break_start_time){
						disabledflag = false;	
					}
					
					if (disabledflag == false) {
						jQuery(this).unwrap();
					}*/
				});
		
				var disabledprop = '';
				var disabledflag = false;	
				// Hides end time options with value less than in the start time
				jQuery('option', $break_end_select).each(function () {
																   
					if (jQuery(this).val() <= break_start_time || jQuery(this).val() > getendtime) {
						//jQuery(this).wrap("<span>").parent().hide();
						jQuery(this).hide();
					}
					
					disabledprop = jQuery(this).attr('disabled');																   
					if(disabledprop == 'disabled' && jQuery(this).val() > break_start_time){
						disabledflag = true;	
					}
					
					if (disabledflag == true) {
						//jQuery(this).wrap("<span>").parent().hide();
						jQuery(this).hide();
					}
				});
				
				var tem = break_start_time.split(':');
				
				var ab = parseInt(tem[0]) + parseInt(1);
				
				if ( ab < 10 ) {
					ab = "0" + ab;
				} 
				var temp = ab+':'+tem[1]+':'+tem[2];
				jQuery($break_end_select).val(temp);
				jQuery('select').selectpicker('refresh');
				
			}).trigger('change');	
	}
	
	/*Add break time*/
		jQuery('.addbreaktimeform')
		.bootstrapValidator({
			message: 'This value is not valid',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
		})
		.on('error.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false); // disable submit buttons on errors
		})
		.on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false); // disable submit buttons on valid
		})
		.on('click','button[name="updatebreaktime"]', function(e) {
			 /*alert('in');
			 if(jQuery('form.provider-bh-hour #currentweek').val() == weekday){
				alert('yesy');
				
			 }else{
				 alert('no');
				return false;	 
			}*/
															  
		})															   
		.on('success.form.bv', function(form) {
		// Prevent form submission
		form.preventDefault();
		// Get the form instance
		var $form = jQuery(form.target);
		// Get the BootstrapValidator instance
		var bv = $form.data('bootstrapValidator');
		
		var data = {
		"action": "bh_addbreaktime",
		"user_id": user_id,
		};
		
		var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
		
		jQuery.ajax({
		
		type: 'POST',
		
		url: ajaxurl,
		
		dataType: "json",
		
		beforeSend: function() {
			jQuery(".alert-success,.alert-danger").remove();
			jQuery('.loading-area').show();
		},
		
		data: formdata,
		
		success:function (data, textStatus) {
			jQuery('.loading-area').hide();
			$form.find('button[type="submit"]').prop('disabled', false);	
			jQuery('.addbreaktimeform').bootstrapValidator('resetForm',true); // Reset form
			if(data['status'] == 'success'){
				jQuery( ".breaktimes-"+currentbreakweekday ).html( data['breaktime_html'] );
				jQuery('#addbreaktimepopup,.sf-couponcode-popup-overlay').fadeOut("slow");
				jQuery( '<div class="alert alert-success">'+data['suc_message']+'</div>' ).insertBefore( 'form.addbreaktimeform' );	
			}
		
		}
		
		});
		});
	
	removebreakslot();
	
	function removebreakslot(){
		jQuery('body').on('click', '.working-hours-remove', function(){
				var $this = jQuery(this);																	 
				var $breakslot = $this.parents('li').data('breakslot');
				var $weekday = $this.parents('li').data('weekday');
				
				var paramdata = {
				  "action": "remove_breakslot",
				  "breakslot": $breakslot,
				  "user_id": user_id,
				  "weekday": $weekday
				};
				
				var formdata = jQuery.param(paramdata);
				
				jQuery.ajax({

					type: 'POST',
			
					url: ajaxurl,
					
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					
					data: formdata,
					
					dataType: "json",
			
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						$this.parents('li').remove();
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.provider-bh-hour" );
					}
			
			
				});
			});	
	}
	
	
	var dayname = 'Mon';
	var tabname = 'monday';
	var mflag = 0;
	var tflag = 0;
	var wflag = 0;
	var thflag = 0;
	var frflag = 0;
	var saflag = 0;
	var suflag = 0;
	/*Click event on subtabs*/
	jQuery('body').on('click', '#subTabHours li a', function(){
		dayname = jQuery(this).attr('href');													
		dayname = dayname.replace("#bh-", "");
		tabname = dayname;
		dayname = dayname.replace("day", "");
		dayname = dayname.substr(0, 1).toUpperCase() + dayname.substr(1);
	});
	
	jQuery('form').on('change', 'select[name="busistarttime"]', function(){
		var $select_starttime = jQuery('select[name="busistarttime"]');
		var $select_endtime = jQuery('select[name="busiendtime"]');
		var start_time       = jQuery(this).val(),
			end_time         = $select_starttime.val(),
			$last_time_entry = jQuery('option:last', $select_starttime);

		$select_endtime.empty();
		$select_endtime.append('<option value="">'+param.endtime+'</option>');
		// case when we click on the not last time entry
		if ($select_starttime[0].selectedIndex < $last_time_entry.index()) {
			// clone and append all next "time_from" time entries to "time_to" list
			jQuery('option', this).each(function () {
				
				if (jQuery(this).val() > start_time) {
					if(jQuery(this).prop('disabled')){
						return false;
					}
					$select_endtime.append(jQuery(this).clone());
				}
				
			});
		// case when we click on the last time entry
		} else {
			$select_endtime.append($last_time_entry.clone()).val($last_time_entry.val());
		}

		var first_value = jQuery('option:first', $select_endtime).val();
		$select_endtime.val(end_time >= first_value ? end_time : first_value);
		jQuery('select[name="busiendtime"] option:last').attr('selected','selected');
		jQuery('.sf-select-box').selectpicker('refresh');
                        
	});
	
	jQuery('body').on('click', 'a.reloadbusihours', function(){
		var currentpage= jQuery(this).data('currentpage');
		window.location = currentpage;
	});
	
	//Save Business Hours
	jQuery('.form-business-hours')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {}
        })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "save_businesshours",
			  "day": tabname,
			};
			
			var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery(".alert-success,.alert-danger").remove();
							jQuery('.loading-area').show();
						},
						
						data: formdata,

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							$form.find('button[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#"+tabname+"-business-hours" );	
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#"+tabname+"-business-hours" );
							}
							
						}

					});
			
        });
	
  });