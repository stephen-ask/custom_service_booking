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
  var dataTable;
  var monarr = [];
  var tuearr = [];
  var wedarr = [];
  var thurarr = [];
  var friarr = [];
  var satarr = [];
  var sunarr = [];
  
  var currentbreakweekday;
  
  jQuery('body').on('click', '.sf-memberbreakpopup-close', function(){
   	jQuery('#addmemberbreaktimepopup,.sf-couponcode-popup-overlay').fadeOut("slow");
  })
	
  jQuery('body').on('click', '.openmemberbreakpopup', function(){
	jQuery('#addmemberbreaktimepopup,.sf-couponcode-popup-overlay').fadeIn();
	jQuery('.alert').remove();
	currentbreakweekday = jQuery(this).data('weekday');
	
	jQuery('.addmemberbreaktimeform input[name="weekday"]').val(currentbreakweekday);
	
	switch(currentbreakweekday){
	case 'monday':
		var breakslot = monarr[0].split('-');
		break;
	case 'tuesday':
		var breakslot = tuearr[0].split('-');
		break;
	case 'wednesday':
		var breakslot = wedarr[0].split('-');
		break;
	case 'thursday':
		var breakslot = thurarr[0].split('-');
		break;
	case 'friday':
		var breakslot = friarr[0].split('-');
		break;
	case 'saturday':
		var breakslot = satarr[0].split('-');
		break;
	case 'sunday':
		var breakslot = sunarr[0].split('-');
		break;	
	}
	
	if(breakslot[0] != ""){
	jQuery('select[name="break_start_time"]').val(breakslot[0]);
	}
	if(breakslot[1] != ""){
	jQuery('select[name="break_end_time"]').val(breakslot[1]);
	}
	jQuery('.sf-select-box').selectpicker('refresh');
	
  })
  
  jQuery('body').on('click', '.addbreaktime', function(){
	jQuery('#addmemberbreaktimepopup,.sf-couponcode-popup-overlay').fadeOut("slow");														   
	var breakstarttime = jQuery('.addmemberbreaktimeform select[name="break_start_time"]').val();
	var breakendtime = jQuery('.addmemberbreaktimeform select[name="break_end_time"]').val();
	
	var breakstarttimetext = jQuery('.addmemberbreaktimeform select[name="break_start_time"] option:selected').text();
	var breakendtimetext = jQuery('.addmemberbreaktimeform select[name="break_end_time"] option:selected').text();
	
	var breakslot = breakstarttime+'-'+breakendtime;
	var breakslottext = breakstarttimetext+' - '+breakendtimetext;
	var breakslothtml = '<li data-weekday="'+currentbreakweekday+'" data-source="'+breakslot+'"><span>'+breakstarttimetext+' - '+breakendtimetext+'</span> <span class="member-breaktime-remove"><i class="fa fa-close"></i></span></li>';
	
	switch(currentbreakweekday){
	case 'monday':
		monarr = [];
		monarr.push(breakslot);
		jQuery('#memberbreak-monday').html(breakslothtml);
		break;
	case 'tuesday':
		tuearr = [];
		tuearr.push(breakslot);
		jQuery('#memberbreak-tuesday').html(breakslothtml);
		break;
	case 'wednesday':
		wedarr = [];
		wedarr.push(breakslot);
		jQuery('#memberbreak-wednesday').html(breakslothtml);
		break;
	case 'thursday':
		thurarr = [];
		thurarr.push(breakslot);
		jQuery('#memberbreak-thursday').html(breakslothtml);
		break;
	case 'friday':
		friarr = [];
		friarr.push(breakslot);
		jQuery('#memberbreak-friday').html(breakslothtml);
		break;
	case 'saturday':
		satarr = [];
		satarr.push(breakslot);
		jQuery('#memberbreak-saturday').html(breakslothtml);
		break;
	case 'sunday':
		sunarr = [];
		sunarr.push(breakslot);
		jQuery('#memberbreak-sunday li span').html(breakslothtml);
		break;	
	}
	
  })
  
  jQuery('ul#memberslot-monday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//monarr = [];
			monarr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			monarr = jQuery.grep(monarr, function(value) {
			  return value != removeItem;
			});
		}
  });	
  
  jQuery('ul#memberslot-tuesday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//tuearr = [];
			tuearr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			tuearr = jQuery.grep(tuearr, function(value) {
			  return value != removeItem;
			});
		}
  });
  
  jQuery('ul#memberslot-wednesday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//wedarr = [];
			wedarr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			wedarr = jQuery.grep(wedarr, function(value) {
			  return value != removeItem;
			});
		}
  });
  
  jQuery('ul#memberslot-thursday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//thurarr = [];
			thurarr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			thurarr = jQuery.grep(thurarr, function(value) {
			  return value != removeItem;
			});
		}
  });
  
  jQuery('ul#memberslot-friday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//friarr = [];
			friarr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			friarr = jQuery.grep(friarr, function(value) {
			  return value != removeItem;
			});
		}
  });
  
  jQuery('ul#memberslot-saturday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//satarr = [];
			satarr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			satarr = jQuery.grep(satarr, function(value) {
			  return value != removeItem;
			});
		}
  });
  
  jQuery('ul#memberslot-sunday').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			//sunarr = [];
			sunarr.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			sunarr = jQuery.grep(sunarr, function(value) {
			  return value != removeItem;
			});
		}
  });
  
  // Edit Member
    jQuery('.member-timeslots')
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
		  "action": "update_member_timeslot",
		  "user_id": user_id,
		  "monarr": monarr,
		  "tuearr": tuearr,
		  "wedarr": wedarr,
		  "thurarr": thurarr,
		  "friarr": friarr,
		  "satarr": satarr,
		  "sunarr": sunarr,
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
						$form.find('input[type="submit"]').prop('disabled', false);
						if(data['status'] == 'success'){
							/*Close the popup window*/
							// Hide the dialog
							$form.parents('.bootbox').modal('hide');
							
							/*Reaload datatable after add new member*/
							dataTable.ajax.reload(null, false);
									
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.member-timeslots" );
						}
						
						
					
					}

				});
		
	});
	
  jQuery('.member-starttime')
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
		  "action": "update_member_starttime",
		  "user_id": user_id,
		  "monarr": monarr,
		  "tuearr": tuearr,
		  "wedarr": wedarr,
		  "thurarr": thurarr,
		  "friarr": friarr,
		  "satarr": satarr,
		  "sunarr": sunarr,
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
						$form.find('input[type="submit"]').prop('disabled', false);
						if(data['status'] == 'success'){
							/*Close the popup window*/
							// Hide the dialog
							$form.parents('.bootbox').modal('hide');
							
							/*Reaload datatable after add new member*/
							dataTable.ajax.reload(null, false);
									
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.member-starttime" );
						}
						
						
					
					}

				});
		
	});	
  
  jQuery('body').on('click', '.editMemberButton', function(){
        jQuery('.loading-area').show();
		// Get the record's ID via attribute
        var memberid = jQuery(this).attr('data-id');
		
		var data = {
			  "action": "load_member",
			  "memberid": memberid,
			  "user_id": user_id
			};
			
	  var formdata = jQuery.param(data);
	  
	  jQuery.ajax({

						type: 'POST',

						url: ajaxurl,

						data: formdata,
						
						dataType: "json",

						success:function (data, textStatus) {
							// Populate the form fields with the data returned from server
							jQuery('#editmember')
								.find('[name="member_fullname"]').val(data['member_fullname']).end()
								.find('[name="member_email"]').val(data['member_email']).end()
								.find('[name="memberid"]').val(memberid).end()
								.find('#memberavataredit').html(data['avatar']).end()
								.find('#editloadservicearea').html(data['newzipcodes']).end()
								.find('#editloadregions').html(data['newregions']).end()
								.find('#editloadservices').html(data['newservices']).end()
								.find('[name="member_phone"]').val(data['member_phone']).end();
							
								var str = data['service_area'];
								var x;
								if(str != "" && str != null){
								var areas = str.split(",");
								for(x in areas){
								jQuery('[name="sarea[]"][value="'+areas[x]+'"]').prop('checked', true).end()
								}
								}
								
								var str = data['selected_regions'];
								var x;
								if(str != "" && str != null){
								var regions = str.split("%%%");
								for(x in regions){
								jQuery('[name="region[]"][value="'+Encoder.htmlDecode(regions[x])+'"]').prop('checked', true).end()
								}
								}

								
							if((data['avatar_id'] != "" && data['avatar_id'] > 0) || data['admin_avatar_id']){
								jQuery('#sfmemberavataruploadedit-dragdrop').addClass('hidden');
							}else{
								jQuery('#sfmemberavataruploadedit-dragdrop').removeClass('hidden');
							}
							// Show the dialog
							bootbox
								.dialog({
									title: param.edit_member,
									message: jQuery('#editmember'),
									show: false // We will show it manually later
								})
								.on('shown.bs.modal', function() {
									jQuery('.loading-area').hide();															   
									jQuery('#editmember')
										.show()                             // Show the login form
										.bootstrapValidator('resetForm'); // Reset form
								})
								.on('hide.bs.modal', function(e) {
									// Bootbox will remove the modal (including the body which contains the login form)
									// after hiding the modal
									// Therefor, we need to backup the form
									jQuery('#editmember').hide().appendTo('body');
								})
								.modal('show');
							
							
						
						}

					});

    });
  
   jQuery('body').on('click', '.setslots', function(){
        jQuery('.loading-area').show();
		// Get the record's ID via attribute
        var memberid = jQuery(this).attr('data-id');
		
		var data = {
			  "action": "load_member_slots",
			  "memberid": memberid,
			  "user_id": user_id
			};
			
	  var formdata = jQuery.param(data);
	  
	  jQuery.ajax({

						type: 'POST',

						url: ajaxurl,

						data: formdata,
						
						dataType: "json",

						success:function (data, textStatus) {
							jQuery('#membertimeslots')
								.find('input[name="memberid"]').val(memberid).end()
								.find('#memberslot-monday').html(data['monday']).end()
								.find('#memberslot-tuesday').html(data['tuesday']).end()
								.find('#memberslot-wednesday').html(data['wednesday']).end()
								.find('#memberslot-thursday').html(data['thursday']).end()
								.find('#memberslot-friday').html(data['friday']).end()
								.find('#memberslot-saturday').html(data['saturday']).end()
								.find('#memberslot-sunday').html(data['sunday']).end();
								
								monarr = [];
								tuearr = [];
								wedarr = [];
								thurarr = [];
								friarr = [];
								satarr = [];
								sunarr = [];
								
								jQuery('ul#memberslot-monday li').each(function(){
									if(jQuery(this).hasClass('active')){
										monarr.push(jQuery(this).attr('data-source'));
									}																  
								});
								
								jQuery('ul#memberslot-tuesday li').each(function(){
									if(jQuery(this).hasClass('active')){
										tuearr.push(jQuery(this).attr('data-source'));
									}																  
								});
								
								jQuery('ul#memberslot-wednesday li').each(function(){
									if(jQuery(this).hasClass('active')){
										wedarr.push(jQuery(this).attr('data-source'));
									}																  
								});
								
								jQuery('ul#memberslot-thursday li').each(function(){
									if(jQuery(this).hasClass('active')){
										thurarr.push(jQuery(this).attr('data-source'));
									}																  
								});
								
								jQuery('ul#memberslot-friday li').each(function(){
									if(jQuery(this).hasClass('active')){
										friarr.push(jQuery(this).attr('data-source'));
									}																  
								});
								
								jQuery('ul#memberslot-saturday li').each(function(){
									if(jQuery(this).hasClass('active')){
										satarr.push(jQuery(this).attr('data-source'));
									}																  
								});
								
								jQuery('ul#memberslot-sunday li').each(function(){
									if(jQuery(this).hasClass('active')){
										sunarr.push(jQuery(this).attr('data-source'));
									}																  
								});
							
							bootbox
								.dialog({
									title: param.set_member_timeslot,
									message: jQuery('#membertimeslots'),
									size: 'large',
								})
								.on('shown.bs.modal', function() {
									jQuery('.loading-area').hide();															   
									jQuery('#membertimeslots')
										.show()
										.bootstrapValidator('resetForm');
								})
								.on('hide.bs.modal', function(e) {
									jQuery('#membertimeslots').hide().appendTo('body');
								})
								.modal('show');
						}

					});

    });
   
   jQuery('body').on('click', '.setstarttime', function(){
        jQuery('.loading-area').show();
		// Get the record's ID via attribute
        var memberid = jQuery(this).attr('data-id');
		
		var data = {
			  "action": "load_member_slots",
			  "memberid": memberid,
			  "user_id": user_id
			};
			
	  var formdata = jQuery.param(data);
	  
	  jQuery.ajax({

						type: 'POST',

						url: ajaxurl,

						data: formdata,
						
						dataType: "json",

						success:function (data, textStatus) {
							jQuery('#memberstarttime')
								.find('input[name="memberid"]').val(memberid).end()
								.find('#memberbreak-monday').html(data['breaktime']['monday']).end()
								.find('#memberbreak-tuesday').html(data['breaktime']['tuesday']).end()
								.find('#memberbreak-wednesday').html(data['breaktime']['wednesday']).end()
								.find('#memberbreak-thursday').html(data['breaktime']['thursday']).end()
								.find('#memberbreak-friday').html(data['breaktime']['friday']).end()
								.find('#memberbreak-saturday').html(data['breaktime']['saturday']).end()
								.find('#memberbreak-sunday').html(data['breaktime']['sunday']).end()
								
								.find('select[name="memberstarttime-monday"]').val(data['starttime']['monday']).end()
								.find('select[name="memberstarttime-tuesday"]').val(data['starttime']['tuesday']).end()
								.find('select[name="memberstarttime-wednesday"]').val(data['starttime']['wednesday']).end()
								.find('select[name="memberstarttime-thursday"]').val(data['starttime']['thursday']).end()
								.find('select[name="memberstarttime-friday"]').val(data['starttime']['friday']).end()
								.find('select[name="memberstarttime-saturday"]').val(data['starttime']['saturday']).end()
								.find('select[name="memberstarttime-sunday"]').val(data['starttime']['sunday']).end()
								
								.find('select[name="memberendtime-monday"]').val(data['endtime']['monday']).end()
								.find('select[name="memberendtime-tuesday"]').val(data['endtime']['tuesday']).end()
								.find('select[name="memberendtime-wednesday"]').val(data['endtime']['wednesday']).end()
								.find('select[name="memberendtime-thursday"]').val(data['endtime']['thursday']).end()
								.find('select[name="memberendtime-friday"]').val(data['endtime']['friday']).end()
								.find('select[name="memberendtime-saturday"]').val(data['endtime']['saturday']).end()
								.find('select[name="memberendtime-sunday"]').val(data['endtime']['sunday']).end();
								
								
								var mondaybreakslot = data['breakstarttime']['monday']+'-'+data['breakendtime']['monday'];
								var tuesdaybreakslot = data['breakstarttime']['tuesday']+'-'+data['breakendtime']['tuesday'];
								var wednesdaybreakslot = data['breakstarttime']['wednesday']+'-'+data['breakendtime']['wednesday'];
								var thursdaybreakslot = data['breakstarttime']['thursday']+'-'+data['breakendtime']['thursday'];
								var fridaybreakslot = data['breakstarttime']['friday']+'-'+data['breakendtime']['friday'];
								var saturdaybreakslot = data['breakstarttime']['saturday']+'-'+data['breakendtime']['saturday'];
								var sundaybreakslot = data['breakstarttime']['sunday']+'-'+data['breakendtime']['sunday'];
								
								monarr = [];
								tuearr = [];
								wedarr = [];
								thurarr = [];
								friarr = [];
								satarr = [];
								sunarr = [];
								
								monarr.push(mondaybreakslot);
								tuearr.push(tuesdaybreakslot);
								wedarr.push(wednesdaybreakslot);
								thurarr.push(thursdaybreakslot);
								friarr.push(fridaybreakslot);
								satarr.push(saturdaybreakslot);
								sunarr.push(sundaybreakslot);
								
								jQuery('.sf-select-box').selectpicker('refresh');
							
							bootbox
								.dialog({
									title: param.set_member_timeslot,
									message: jQuery('#memberstarttime'),
									size: 'large',
								})
								.on('shown.bs.modal', function() {
									jQuery('.loading-area').hide();															   
									jQuery('#memberstarttime')
										.show()
										.bootstrapValidator('resetForm');
								})
								.on('hide.bs.modal', function(e) {
									jQuery('#memberstarttime').hide().appendTo('body');
								})
								.modal('show');
						}

					});

    });
  
   jQuery('#addmember').on('hide.bs.modal', function() {
		jQuery('.add-new-member').bootstrapValidator('resetForm',true); // Reset form
	});
   //When Show modal popup box for add new member
   jQuery('#addmember').on('show.bs.modal', function() {
		
		var data = {
			  "action": "loadserviceareas",
			  "user_id": user_id
			};
			
		var formdata = jQuery.param(data);
		
		jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},
						
						data: formdata,

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
							jQuery('#loadservicearea').html(data['servicearea']);
							jQuery('#loadregions').html(data['regions']);
							jQuery('#loadservices').html(data['services']);
							}
						
						}

					});
   });
   
   // Save New Member
    jQuery('.add-new-member')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				member_fullname: {
					validators: {
						notEmpty: {
							message: param.member
						}
					}
				},
				member_email: {
                validators: {
                    notEmpty: {
														message: param.req
													},
					emailAddress: {
                        message: param.signup_user_email
                    }
					}
				},
            }
        })
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "add_new_member",
			  "user_id": user_id
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
							$form.find('input[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								jQuery("#memberavatar").html('');
								jQuery("#member_fullname").val('');
								jQuery("#member_email").val('');
								jQuery("#member_phone").val('');
								jQuery('input[type="checkbox"][name="sarea\\[\\]"]:checked').prop('checked',true);
								/*Close the popup window*/
								jQuery('#addmember').modal('hide');
								jQuery('#sfmemberavatarupload-dragdrop').removeClass('hidden');
								
								
								/*Reaload datatable after add new member*/
								dataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-new-member" );
							}
							
							
						
						}

					});
			
        });
		
	// Edit Member
    jQuery('.edit-member')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				member_fullname: {
					validators: {
						notEmpty: {
							message: param.member
						}
					}
				},
				member_email: {
                validators: {
                    notEmpty: {
														message: param.req
													},
					emailAddress: {
                        message: param.signup_user_email
                    }
					}
				},
            }
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
			  "action": "edit_member",
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
							$form.find('input[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								jQuery("#member_fullname").val('');
								jQuery("#member_email").val('');
								jQuery("#member_phone").val('');
								jQuery('input[type="checkbox"][name="sarea\\[\\]"]:checked').prop('checked',true);
								/*Close the popup window*/
								// Hide the dialog
				                $form.parents('.bootbox').modal('hide');
								
								/*Reaload datatable after add new member*/
								dataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-member" );
							}
							
							
						
						}

					});
			
        });	
	
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#team-members'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#members-grid' ) ) {
			dataTable = jQuery('#members-grid').DataTable( {
	"serverSide": true,
	"bAutoWidth": false,
	"columnDefs": [ {
		  "targets": 0,
		  "orderable": false,
		  "searchable": false
		   
		} ],
	"processing": true,
	"language": {
					"processing": "<div></div><div></div><div></div><div></div><div></div>",
					"emptyTable":     param.empty_table,
					"search":         param.dt_search+":",
					"lengthMenu":     param.dt_show + " _MENU_ " + param.dt_entries,
					"info":           param.dt_showing + " _START_ " + param.dt_to + " _END_ " + param.dt_of + " _TOTAL_ " + param.dt_entries,
					"infoEmpty":      param.dt_showing + " _START_ " + param.dt_to + " _END_ " + param.dt_of + " _TOTAL_ " + param.dt_entries,
					"paginate": {
						first:      param.dt_first,
						previous:   param.dt_previous,
						next:       param.dt_next,
						last:       param.dt_last,
					},
				},
	"ajax":{
		url :ajaxurl, // json datasource
		type: "post",  // method  , by default get
		data: {"action": "get_members","user_id": user_id},
		error: function(){  // error handling
			jQuery(".members-grid-error").html("");
			jQuery("#members-grid").append('<tbody class="members-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
			jQuery("#members-grid_processing").css("display","none");
			
		}
	}
	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	
	jQuery("#bulkMemberDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteMemberRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
    
    jQuery('#deleteMemberTriger').on("click", function(event){ // triggering delete one by one
		
		
			  if( jQuery('.deleteMemberRow:checked').length > 0 ){  // at-least one checkbox checked
           
		   bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
		   var ids = [];
            jQuery('.deleteMemberRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_members", data_ids:ids_string},
                success: function(result) {
                    dataTable.draw(); // redrawing datatable
                },
                async:false
            });
        	}
		}); 
		
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
		
        
    });
	
	removememberbreakslot();
	
	function removememberbreakslot(){
		jQuery('body').on('click', '.member-breaktime-remove', function(){
				var $this = jQuery(this);																	 
				var $breakslot = $this.parents('li').data('source');
				var $weekday = $this.parents('li').data('weekday');
				var $memberid = $this.parents('li').data('memberid');
				
				var paramdata = {
				  "action": "remove_member_breaktime",
				  "breakslot": $breakslot,
				  "user_id": user_id,
				  "memberid": $memberid,
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
						switch($weekday){
						case 'monday':
							monarr = [];
							monarr.push('-');
							break;
						case 'tuesday':
							tuearr = [];
							tuearr.push('-');
							break;
						case 'wednesday':
							wedarr = [];
							wedarr.push('-');
							break;
						case 'thursday':
							thurarr = [];
							thurarr.push('-');
							break;
						case 'friday':
							friarr = [];
							friarr.push('-');
							break;
						case 'saturday':
							satarr = [];
							satarr.push('-');
							break;
						case 'sunday':
							sunarr = [];
							sunarr.push('-');
							break;	
						}
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#memberbreak-"+$weekday );
					}
			
			
				});
			});	
	}
	

	
  });