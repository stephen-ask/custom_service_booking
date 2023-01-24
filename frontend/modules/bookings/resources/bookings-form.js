/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/

var invoicedataTable = '';

var membersavailable;

var currentmemberid;



/*Get Time Slots*/



jQuery('body').on('click', '.edit-booking ul.timeslots li', function(){

			//jQuery(this).addClass('active').siblings().removeClass('active');

			jQuery('ul.timeslots li').removeClass('active');

			jQuery(this).addClass('active');

			

			service_finder_resetMembers();

			var slot = jQuery(this).attr('data-source');

			jQuery("#boking-slot").val(slot);

			jQuery("#provider").val(provider_id);

			

			if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){

			var bookingid = jQuery(this).data('bookingid');

			var data = {

				  "action": "load_members",

				  "zipcode": zipcode,

				  "provider_id": provider_id,

				  "date": date,

				  "slot": slot,

				  'customeredit': 'yes',

				  'bookingid': bookingid

				};

			var formdata = jQuery.param(data);

			  

			jQuery.ajax({



				type: 'POST',



				url: ajaxurl,



				data: formdata,

				

				dataType: "json",

				

				beforeSend: function() {

					jQuery('.loading-area').show();

				},



				success:function (data, textStatus) {

						jQuery('.loading-area').hide();

						jQuery("#editbooking").find(".alert").remove();

						 if(data != null){

							if(data['status'] == 'success'){

								jQuery("#editbooking").find("#members").html(data['members']);

								jQuery("#editbooking").find("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>');

								jQuery('.display-ratings').rating();

								jQuery('.sf-show-rating').show();

							}

						}

				}



			});	

			}

			

			

		});	



jQuery('#edit-servicedate').on('click', 'ul.timeslots li', function(){

			//jQuery(this).addClass('active').siblings().removeClass('active');

			jQuery('ul.timeslots li').removeClass('active');

			jQuery(this).addClass('active');

			

			var slot = jQuery(this).attr('data-source');

			jQuery('#edit-servicedate input[name="slot"]').val(slot);

			

		});	



/*Staff Member click event*/

		jQuery('body').on('click', '.staff-member .sf-element-bx', function(){																		

			var memberid = jQuery(this).attr("data-id");																

			jQuery("#memberid").val(memberid);

			jQuery(".staff-member .sf-element-bx").removeClass('selected');

			jQuery(this).addClass('selected');

			jQuery(this).prop("checked",status);

			jQuery('.anymember').prop("checked",false);

		});

		/*Select any staff member*/

		jQuery('body').on('click', '.anymember', function(){				

			jQuery(".staff-member .sf-element-bx").removeClass('selected');

			jQuery("#memberid").val('');

		});

							

//View Bookings

jQuery('body').on('click', '.viewBookings', function(){

											  

	var bid = jQuery(this).attr('data-id');

	var upcoming = jQuery(this).attr('data-upcoming');

	if(upcoming == 'yes'){

		var flag = 1;	

	}else{

		var flag = 0;	

	}

	viewBookings(bid,flag);

	

});



function viewBookings(bid,flag = 1){

	

	jQuery('#bookings-grid_wrapper').addClass('hidden');

	jQuery('#booking-details').removeClass('hidden');

	

	var data = {

	  "action": "booking_details",

	  "bookingid": bid,

	  "flag": flag,

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

					

					jQuery('#booking-details').html(data);

					

					jQuery('.display-ratings').rating();

					jQuery('.sf-show-rating').show();

				}



			});		

}



//Close details

jQuery('body').on('click', '.closeDetails', function(){

	jQuery('#bookings-grid').removeClass('hidden');														 

	jQuery('#pastbooking-details').addClass('hidden fade in');

	jQuery('#bookings-grid_wrapper').removeClass('hidden');

	

	jQuery('#pastbookings-customer-grid_wrapper').removeClass('hidden');

	

	

	jQuery('#booking-details').addClass('hidden fade in');

	jQuery('#upcomingbookings-customer-grid_wrapper').removeClass('hidden');
	
	jQuery('.bookingOptionSelect').val('');
	jQuery('.sf-select-box').selectpicker('refresh');

});



 //Load Assign Member Popup

 jQuery('body').on('click', '.assignButton', function(){

	jQuery('.loading-area').show();														  

	// Get the record's ID via attribute

	var id = jQuery(this).attr('data-id');

	var data = {

		  "action": "load_allmembers",

		  "bookingid": id,

		};

		

  var formdata = jQuery.param(data);

  

  jQuery.ajax({



					type: 'POST',



					url: ajaxurl,



					data: formdata,

					

					dataType: "json",



					success:function (data, textStatus) {

						// Populate the form fields with the data returned from server

						jQuery('#assignmember')

							.find('#bookingid').val(id).end()

							.find('#memberid').val('').end()

							.find('#allmembers').html(data['members']).end();

						// Show the dialog

						bootbox

							.dialog({

								title: param.assign_member,

								message: jQuery('#assignmember'),

								show: false // We will show it manually later

							})

							.on('shown.bs.modal', function() {

								jQuery('.loading-area').hide();															   

								jQuery('#assignmember')

									.show()                             // Show the login form

									.bootstrapValidator('resetForm'); // Reset form

							})

							.on('hide.bs.modal', function(e) {

								// Bootbox will remove the modal (including the body which contains the login form)

								// after hiding the modal

								// Therefor, we need to backup the form

								jQuery('#assignmember').hide().appendTo('body');

							})

							.modal('show');

						

						

					

					}



				});



});

 

 //Edit Assign Member Popup

 jQuery('body').on('click', '.editAssignButton', function(){

	jQuery('.loading-area').show();

	// Get the record's ID via attribute

	var ids = jQuery(this).attr('data-id');

	var res = ids.split("-");

	var data = {

		  "action": "load_allmembers",

		  "bookingid": res[0],

		  "memberid": res[1],

		};

		

  var formdata = jQuery.param(data);

  

  jQuery.ajax({



					type: 'POST',



					url: ajaxurl,



					data: formdata,

					

					dataType: "json",



					success:function (data, textStatus) {

						// Populate the form fields with the data returned from server

						jQuery('#assignmember')

							.find('#bookingid').val(res[0]).end()

							.find('#memberid').val(res[1]).end()

							.find('#allmembers').html(data['members']).end();

						// Show the dialog

						bootbox

							.dialog({

								title: param.assign_member,

								message: jQuery('#assignmember'),

								show: false // We will show it manually later

							})

							.on('shown.bs.modal', function() {

								jQuery('.loading-area').hide();

								jQuery('#assignmember')

									.show()                             // Show the login form

									.bootstrapValidator('resetForm'); // Reset form

							})

							.on('hide.bs.modal', function(e) {

								// Bootbox will remove the modal (including the body which contains the login form)

								// after hiding the modal

								// Therefor, we need to backup the form

								jQuery('#assignmember').hide().appendTo('body');

							})

							.modal('show');

						

						

					

					}



				});



});

 

 /*Select Action*/

 jQuery('body').on('change', '.bookingOptionSelect', function(){

 

											

 var option = jQuery(this).val();

 var bid = jQuery(this).attr('data-bid');

 if(option != ""){

 jQuery('.loading-area').show();															

 }

 if(option == 'invoice'){

	window.location = myaccount+'?action=invoice&bookingid='+bid; 

 }else if(option == 'addfeedback'){

	feedbookingid = bid;

 

	 var data = {

			  "action": "get_ratingbox",

			  "feedbookingid": feedbookingid,

			};

			

	  var formdata = jQuery.param(data);

	  

	  jQuery.ajax({



						type: 'POST',



						url: ajaxurl,



						data: formdata,

						

						success:function (data, textStatus) {

							jQuery('#customrating').html(data);

							

							jQuery(".add-custom-rating").rating({

								stars: 5,

								starCaptions: function(val) {

									return ' ';

								},

								starCaptionClasses: function(val) {

									if (val <= 1) {

										return 'aon-icon-angry';

									} else if (val <= 2){

										return 'aon-icon-cry';

									} else if (val <= 3){

										return 'aon-icon-sad';

									} else if (val <= 4){

										return 'aon-icon-happy';

									} else if (val <= 5){

										return 'aon-icon-awesome';

									}

								},

								clearCaptionClass: '',

								clearButton: '',

								clearCaption: '',

								hoverOnClear: false

							});

						}



					});

	

	// Show the dialog

	bootbox

		.dialog({

			title: param.add_feedback,

			message: jQuery('#addFeedback'),

			show: false // We will show it manually later

		})

		.on('shown.bs.modal', function() {

			jQuery('.loading-area').hide();

			jQuery('#addFeedback')

				.show()                             // Show the login form

				.bootstrapValidator('resetForm'); // Reset form

		})

		.on('hide.bs.modal', function(e) {

			// Bootbox will remove the modal (including the body which contains the login form)

			// after hiding the modal

			// Therefor, we need to backup the form

			jQuery('#addFeedback').hide().appendTo('body');

		})

		.modal('show');	

 

 }

 else if(option == 'viewfeedback'){

	feedbookingid = bid;

 

 var data = {

		  "action": "show_feedback",

		  "feedbookingid": feedbookingid,

		};

		

  var formdata = jQuery.param(data);

  

  jQuery.ajax({



					type: 'POST',



					url: ajaxurl,



					data: formdata,

					

					dataType: "json",



					success:function (data, textStatus) {

						// Populate the form fields with the data returned from server

						if(data['ratingtype'] == 'custom'){

							jQuery('#viewFeedback')

							.find('#showcomment').html(data['comment']).end();

							

							jQuery('#displaycustomrating').html(data['customrating']);

							jQuery('.display-ratings').rating();

							jQuery('.sf-show-rating').show();

						}else{

						jQuery('#viewFeedback')

							.find('#show-comment-rating').rating('update', data['rating']).end()

							.find('#showcomment').html(data['comment']).end();

							

						jQuery("#show-comment-rating, #show-booking-rating").rating({

							showClear:false, 

							disabled:true,																	  

							starCaptions: function(val) {

								if (val < 3) {

									return val;

								} else {

									return 'high';

								}

							},

							starCaptionClasses: function(val) {

								if (val < 3) {

									return 'label label-danger';

								} else {

									return 'label label-success';

								}

							},

							hoverOnClear: false

						});	

						}



						// Show the dialog

						bootbox

							.dialog({

								title: param.feedback,

								message: jQuery('#viewFeedback'),

								show: false // We will show it manually later

							})

							.on('shown.bs.modal', function() {

								jQuery('.loading-area').hide();

								jQuery('#viewFeedback')

									.show()                             // Show the login form

									.bootstrapValidator('resetForm'); // Reset form

							})

							.on('hide.bs.modal', function(e) {

								// Bootbox will remove the modal (including the body which contains the login form)

								// after hiding the modal

								// Therefor, we need to backup the form

								jQuery('#viewFeedback').hide().appendTo('body');

							})

							.modal('show');

						

					}



				});

  

 

 }

 else if(option == 'booking'){

	

	var upcoming = jQuery(this).attr('data-upcoming');

	if(upcoming == 'yes'){

		var flag = 1;	

		jQuery('#upcomingbookings-customer-grid_wrapper').addClass('hidden');

		jQuery('#booking-details').removeClass('hidden');

	}else{

		var flag = 0;	

		jQuery('#pastbookings-customer-grid_wrapper').addClass('hidden');

		jQuery('#pastbooking-details').removeClass('hidden');

	}

	

	var data = {

		  "action": "booking_details",

		  "bookingid": bid,

		  "flag": flag

		};

		

		var data = jQuery.param(data);

		

		jQuery.ajax({



					type: 'POST',



					url: ajaxurl,

					

					data: data,

					

					beforeSend: function() {

					},



					success:function (data, textStatus) {

						jQuery('.loading-area').hide();

						if(upcoming == 'yes'){

							jQuery('#booking-details').html(data);

						}else{

							jQuery('#pastbooking-details').html(data);

						}

						jQuery('.display-ratings').rating();

						jQuery('.sf-show-rating').show();

					}



				});

  

 }

 else if(option == 'editbooking'){

	staffmember = '';

	caps = '';

	provider_id = '';

	date = '';

	zipcode = '';

	jQuery("#loadcalendar").html('<div id="editbooking-calendar"></div>');													

	jQuery('.loading-area').show();												

	var data = {

		  "action": "editbooking",

		  "bookingid": bid,

		  "customereditbooking": 'yes',

		};

  var formdata = jQuery.param(data);



  jQuery.ajax({



					type: 'POST',

					url: ajaxurl,

					data: formdata,

					dataType: "json",

					success:function (data, textStatus) {

						

						if(data['memberid'] == 0){

							var checked = 'checked="checked"';

						}else{

							var checked = '';		

						}

						

						if(data['members'] != ""){

						jQuery('#editbooking')

						.find('.timeslots').html(data['slots']).end()

						.find('#members').html(data['members']).end()

						.find("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>').end();

						}else{

						jQuery('#editbooking')

						.find('.timeslots').html(data['slots']).end()

						.find('#members').html(data['members']).end();

						}

						

						jQuery('.display-ratings').rating();

						jQuery('.sf-show-rating').show();

						

						staffmember = data['staffmember'];

						caps = data['caps'];

						provider_id = data['provider_id'];

						zipcode = data['zipcode'];

						date = data['date'];

						service_finder_setCookie('setselecteddate', data['date']);

						

						jQuery("#booking_id").val(bid);

						jQuery("#provider").val(provider_id);

						jQuery("#date").val(date);

						jQuery("#memberid").val(data['memberid']);

						jQuery("#boking-slot").val(data['activeslots']);

						

						daynumarr = jQuery.parseJSON(data['dayavlnum']);

						datearr = jQuery.parseJSON(data['dates']);

						bookedarr = jQuery.parseJSON(data['bookeddates']);

						//allocatedarr = jQuery.parseJSON(data['allocateddates']);

						

						jQuery("#editbooking-calendar").zabuto_calendar({

							today: true,

							show_previous: true,

							date:data['date'],

							selectedday:data['daynum'],

							month : data['month'],

							mode : 'edit',

							daynum : daynumarr,

							datearr : datearr,

							bookedarr : bookedarr,

							year: data['year'],

							action: function () {

											jQuery('.dow-clickable').removeClass("selected");

											jQuery(this).addClass("selected");

											date = jQuery("#" + this.id).data("date");

											service_finder_setCookie('setselecteddate', date);

											jQuery("#date").val(date);

											jQuery("#memberid").val('');

											jQuery("#boking-slot").val('');

											if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){

												return service_finder_timeslotCallback(this.id, data['provider_id'],data['totalhours'],data['bookingid']);

											}else if(jQuery.inArray("availability", caps) > -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){

												return service_finder_timeslotCallback(this.id, data['provider_id'],data['totalhours'],data['bookingid']);

											}else if(jQuery.inArray("availability", caps) == -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){

												return service_finder_memberCallback(this.id, data['provider_id'],data['zipcode'],data['bookingid']);	

											}else if(jQuery.inArray("availability", caps) == -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){

												jQuery('#selecteddate').attr('data-seldate',date);	

											}

										},

						});	



						// Show the dialog

						bootbox

							.dialog({

								title: param.edit_booking,

								message: jQuery('#editbooking'),

								show: false // We will show it manually later

							})

							.on('shown.bs.modal', function() {

								jQuery('.loading-area').hide();

								jQuery('#editbooking')

									.show()                             // Show the login form

									.bootstrapValidator('resetForm'); // Reset form



							})

							.on('hide.bs.modal', function(e) {

								// Bootbox will remove the modal (including the body which contains the login form)

								// after hiding the modal

								// Therefor, we need to backup the form

								jQuery('#editbooking').hide().appendTo('body');



							})

							.modal('show');

							

					}



				});

 

}

 else if(option == 'editservices'){

	jQuery('.booking-list').hide();	 

	jQuery('.services-list').show();	 

	

	var editbookingid = jQuery(this).data('bookingid');	 

	

	loadbookedservices(bid);

 }



				   

});

	 

  function loadbookedservices(bid){

	  

	jQuery('.loading-area').hide();  



	if ( ! jQuery.fn.DataTable.isDataTable( '#services-grid' ) ) {

	servicesdataTable = jQuery('#services-grid').DataTable( {
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

	

		data: {"action": "get_booked_services","editbookingid": bid},

	

		error: function(){  // error handling

	

			jQuery(".services-grid-error").html("");

	

			jQuery("#services-grid").append('<tbody class="services-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

	

			jQuery("#services-grid_processing").css("display","none");

	

			

	

		}

	

	}

	

	} );

			jQuery('.sf-select-box').selectpicker('refresh');

			}
	}

  

  jQuery('#edit-servicedate').on('click', '.update-service-date', function(){

		var dates = jQuery('#edit-servicedate input[name="dates"]').val();

		var memberid = jQuery('#edit-servicedate select[name="members_list"]').val();

		var costtype = jQuery('#edit-servicedate input[name="costtype"]').val();

		var totalnumber = jQuery('#edit-servicedate input[name="totalnumber"]').val();

		var providerid = jQuery('#edit-servicedate input[name="providerid"]').val();

		var bookingid = jQuery('#edit-servicedate input[name="bookingid"]').val();

		var serviceid = jQuery('#edit-servicedate input[name="serviceid"]').val();

		var slot = jQuery('#edit-servicedate input[name="slot"]').val();

		var date = jQuery('#edit-servicedate input[name="date"]').val();

		

		if(membersavailable == false){

			memberid = currentmemberid

		}



		var data = {

					  "action": "update_service_schedule",

					  "bookingid": bookingid,

					  "serviceid": serviceid,

					  "memberid": memberid,

					  "costtype": costtype,

					  "totalnumber": totalnumber,

					  "providerid": providerid,

					  "dates": dates,

					  "date": date,

					  "slot": slot,

					};



		var formdata = jQuery.param(data);



		jQuery.ajax({

			type: 'POST',

			url: ajaxurl,

			data: formdata,

			dataType: "json",

			beforeSend: function() {

				jQuery('.loading-area').show();

			},

			success:function (data, textStatus) {

					jQuery('.loading-area').hide();

					if(data != null){

						if(data['status'] == 'success'){

							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#services-grid_wrapper" );

							jQuery('#edit-servicedate').modal('hide');

							jQuery('select[name="members_list"]').html(data['members']);

							servicesdataTable.ajax.reload(null, false);

							jQuery('.sf-select-box').selectpicker('refresh');

						}



					}

			}



		});	

  });																

  

  jQuery('.services-list').on('click', '.editservice', function(){

		jQuery(".alert-success,.alert-danger").remove();



		var bookedserviceid = jQuery(this).data('bookedserviceid');

		var serviceid = jQuery(this).data('serviceid');

		var memberid = jQuery(this).data('memberid');

		var bookingid = jQuery(this).data('bookingid');

		var providerid = jQuery(this).data('providerid');

		var costtype = jQuery(this).data('costtype');

		var totalnumber = jQuery(this).data('totalnumber');

		membersavailable = jQuery(this).data('membersavailable');

		currentmemberid = memberid;

		

		jQuery('#edit-servicedate input[name="providerid"]').val(providerid);

		jQuery('#edit-servicedate input[name="bookingid"]').val(bookingid);

		jQuery('#edit-servicedate input[name="memberid"]').val(memberid);

		jQuery('#edit-servicedate input[name="serviceid"]').val(serviceid);

		jQuery('#edit-servicedate input[name="bookedserviceid"]').val(bookedserviceid);

		jQuery('#edit-servicedate input[name="costtype"]').val(costtype);

		jQuery('#edit-servicedate input[name="totalnumber"]').val(totalnumber);

		

		

		show_hide_stepbox('stepbox1','hide');

		show_hide_stepbox('stepbox2','hide');

		

		if( membersavailable == true ){

		jQuery('#memberslist').show();

	

		load_members_list(bookingid,serviceid,memberid);	

		show_hide_stepbox('stepbox1','show');

		}else{

		reset_calendar(memberid);

		

		show_hide_stepbox('stepbox1','hide');

		show_hide_stepbox('stepbox2','show');

		}

		

		jQuery('#edit-servicedate').modal('show'); 



	});

  

    function load_members_list(bookingid,serviceid,memberid){

	

		var data = {

					  "action": "load_editmembers_list",

					  "bookingid": bookingid,

					  "serviceid": serviceid,

					  "memberid": memberid,

					};



		var formdata = jQuery.param(data);



		jQuery.ajax({

			type: 'POST',

			url: ajaxurl,

			data: formdata,

			dataType: "json",

			beforeSend: function() {

				jQuery('.loading-area').show();

			},

			success:function (data, textStatus) {

					jQuery('.loading-area').hide();

					if(data != null){

						if(data['status'] == 'success'){

							jQuery('select[name="members_list"]').html(data['members']);

							jQuery('.sf-select-box').selectpicker('refresh');

						}



					}

			}



		});		

	}

	

	jQuery('#edit-servicedate').on('click', 'input[name="nextstepbox"]', function(){

		jQuery(".alert-success,.alert-danger").remove();																				   

		

		

		var memberid = jQuery('#edit-servicedate select[name="members_list"]').val();

		

		reset_calendar(memberid);

		

		show_hide_stepbox('stepbox1','hide');

		show_hide_stepbox('stepbox2','show');

		

	});

	

	function reset_calendar(memberid = ''){

	var provider_id = jQuery('#edit-servicedate input[name="providerid"]').val();

	var booking_id = jQuery('#edit-servicedate input[name="bookingid"]').val();

	var member_id = jQuery('#edit-servicedate input[name="memberid"]').val();

	var service_id = jQuery('#edit-servicedate input[name="serviceid"]').val();

	var costtype = jQuery('#edit-servicedate input[name="costtype"]').val();

	var totalnumber = jQuery('#edit-servicedate input[name="totalnumber"]').val();

	

	jQuery("#loadservicecalendar").html('<div id="service-calendar"></div>');



	var data = {



				  "action": "reset_editbookingcalendar",

				  "provider_id": provider_id,

				  "member_id": memberid,

				  "booking_id": booking_id,

				  "service_id": service_id,

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



					daynumarr = jQuery.parseJSON(data['daynum']);



					datearr = jQuery.parseJSON(data['dates']);



					bookedarr = jQuery.parseJSON(data['bookeddates']);

					

					selecteddatesarr = jQuery.parseJSON(data['selecteddates']);



					service_finder_deleteCookie('setselecteddate');

					

					jQuery("#loadservicecalendar").html('<div id="service-calendar"></div>');

					

					jQuery('.timeslots').html(data['slots']);

					

					if(data['selecteddatesstr'] != ""){

					jQuery('#edit-servicedate input[name="dates"]').val(data['selecteddatesstr']);

					}

					

					if(data['selecteddate'] != ""){

					jQuery('#edit-servicedate input[name="date"]').val(data['selecteddate']);

					}

					if(data['selectedslot'] != ""){

					jQuery('#edit-servicedate input[name="slot"]').val(data['selectedslot']);						

					}

					

					service_finder_setCookie('setselecteddate', data['selecteddate']);



					jQuery("#service-calendar").zabuto_calendar({



						today: true,



						show_previous: true,



						mode : 'edit',



						daynum : daynumarr,

						

						date:data['selecteddate'],

						

						selectedday:data['daynum'],

						

						month : data['month'],

						

						year: data['year'],



						datearr : datearr,

						

						selecteddatesarr : selecteddatesarr,



						bookedarr : bookedarr,



                        action: function () {

							

							jQuery('.alert').remove();

							

							jQuery('.dow-clickable').removeClass("selected");



							jQuery(this).addClass("selected");



							date = jQuery("#" + this.id).data("date");



							service_finder_setCookie('setselecteddate', date); 

							

							var dates = '';

							

							return service_finder_edittimeslotCallback(this.id, provider_id, totalnumber, service_id, datearr, costtype, booking_id);



                        },



                    });



					



					}else if(data['status'] == 'error'){



					}

				}

			});	

	}

	

	function service_finder_get_bookingdays(id,totalnumber,date,paramsid,datearr = '') {

	   

	  var data = {

			  "action": "get_editbookingdays",

			  "totalnumber": totalnumber,

			  "startdate": date,

			  "datearr": datearr,

			};

		var formdata = jQuery.param(data);

		  

		jQuery.ajax({



			type: 'POST',



			url: ajaxurl,



			data: formdata,

			

			dataType: "json",

			

			beforeSend: function() {

				jQuery('.loading-area').show();

				jQuery(".alert-success,.alert-danger").remove();

			},



			success:function (data, textStatus) {

				jQuery('.dow-clickable').removeClass("selected");

				if(data['status'] == 'success'){

				singleservicehour = data['bookingdates'].length;

				

				jQuery('#servicedate-Modal input[name="providerhours"]').val(singleservicehour);

				if(singleservicehour > 0){

				jQuery("#serbx-" + paramsid).data("hours",singleservicehour);

				}

				var dates = '';

				

				for (var key in data['bookingdates']) {

				if (data['bookingdates'].hasOwnProperty(key)) {

					dates = dates + data['bookingdates'][key] + '##';

					var sdate = id.replace(date, data['bookingdates'][key]);

					jQuery("#" + sdate).addClass("selected");

				}

				}

				jQuery('#edit-servicedate input[name="dates"]').val(dates);

				jQuery('.loading-area').hide();	

				}else if(data['status'] == 'error'){

					jQuery('.loading-area').hide();	

					jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#loadservicecalendar" );

				}

				

			}



		}); 

   }



  	/*Timeslot callback function*/

	function service_finder_edittimeslotCallback(id, provider_id, totalnumber, paramsid = '', datearr = '', costtype = '', bookingid) {



		var date = jQuery("#" + id).data("date");

		if(costtype == 'days'){

		service_finder_get_bookingdays(id,totalnumber,date,paramsid,datearr);

		}else{



		jQuery('#edit-servicedate input[name="date"]').val(date);

		

		var data = {



			  "action": "get_bookingtimeslot",

			  "seldate": date,

			  "provider_id": provider_id,

			  "totalhours": totalnumber,

			  "editbooking": 'yes',

			  "bookingid": bookingid,

			};



		var formdata = jQuery.param(data);



		jQuery.ajax({

			type: 'POST',

			url: ajaxurl,

			data: formdata,

			beforeSend: function() {

				jQuery('.loading-area').show();

			},

			success:function (data, textStatus) {



				jQuery('.loading-area').hide();



				jQuery('.timeslots').html(data);



			}







		});



		}

		return true;



	}

	

	jQuery('body').on('click', '.backtobookings', function(){

		jQuery('.booking-list').show();	 

		jQuery('.services-list').hide();			
		
		servicesdataTable.destroy();
		
		jQuery('.bookingOptionSelect').val('');
		jQuery('.sf-select-box').selectpicker('refresh');

	});	

	

	jQuery('#edit-servicedate').on('click', 'input[name="backstepbox"]', function(){

		

		show_hide_stepbox('stepbox1','show');

		show_hide_stepbox('stepbox2','hide');

																						 

	});

	

	function show_hide_stepbox(id,visibility){

		if(visibility == 'show'){

			jQuery('#'+id).show();	

			

			if(id == 'stepbox1'){

				jQuery('#edit-servicedate .update-service-date').hide();

				jQuery('#edit-servicedate input[name="nextstepbox"]').show();

				jQuery('#edit-servicedate input[name="backstepbox"]').hide();

			}else if(id == 'stepbox2'){

				jQuery('#edit-servicedate .update-service-date').show();

				jQuery('#edit-servicedate input[name="nextstepbox"]').hide();

				jQuery('#edit-servicedate input[name="backstepbox"]').show();

			}

		}else if(visibility == 'hide'){

			jQuery('#'+id).hide();

			

			if(id == 'stepbox1'){

				jQuery('#edit-servicedate .update-service-date').show();

				jQuery('#edit-servicedate input[name="backstepbox"]').show();

				jQuery('#edit-servicedate input[name="nextstepbox"]').hide();

			}else if(id == 'stepbox2'){

				jQuery('#edit-servicedate .update-service-date').hide();

				jQuery('#edit-servicedate input[name="backstepbox"]').hide();

				jQuery('#edit-servicedate input[name="nextstepbox"]').show();

			}

		}

	}

	 



// When the browser is ready...

  jQuery(function() {

	'use strict';

	

	var dataTable;

	var servicesdataTable;
	
	var CustomerUpcomingdataTable;
	var CustomerPastdataTable;



	var customer_flag = 1;

	var status_flag = 1;

	

	var ratingflag = 1;

	

	var rating = '';

	

	if(bookingstab == "yes" && viewbookingid > 0){

		load_booking_datatable('#bookings');

		viewBookings(viewbookingid);

	}else{

		load_booking_datatable('#bookings');

	}

																

	

	//Approve wired booking

	jQuery('body').on('click', '.approvewiredbooking', function(){

		var bookingid = jQuery(this).data('bookingid');													 

		

		var data = {

					  "action": "wired_booking_approval",

					  "bookingid": bookingid,

					};

				var formdata = jQuery.param(data);

				  

				jQuery.ajax({

	

					type: 'POST',

	

					url: ajaxurl,

	

					data: formdata,

					

					dataType: "json",

					

					beforeSend: function() {

						jQuery('.loading-area').show();

					},

	

					success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){

								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( ".bookings-grid_wrapper" );	

								dataTable.ajax.reload(null, false);
								jQuery('.sf-select-box').selectpicker('refresh');

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( ".bookings-grid_wrapper" );

							}

					}

	

				});

	});

	

	//Cancel Booking

	jQuery('body').on('click', '.cancelbooking', function(){

	var currentclass = jQuery('body').hasClass('modal-open');
	
	var bid = jQuery(this).attr('data-id');														  

	bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

			  var data = {

			  "action": "cancel_booking",

			  "bookingid": bid,

			};

			

			var data = jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						data: data,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.loading-area').show();

							jQuery('.alert').remove();

						},



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['role'] == 'provider' || data['role'] == 'administrator'){

							jQuery('#booking-details').addClass('hidden fade in');

							jQuery('#bookings-grid_wrapper').removeClass('hidden');

							dataTable.ajax.reload(null, false);	
							jQuery('.sf-select-box').selectpicker('refresh');

							}else{

							jQuery('#booking-details').addClass('hidden fade in');

							jQuery('#upcomingbookings-customer-grid_wrapper').removeClass('hidden');

							CustomerUpcomingdataTable.ajax.reload(null, false);
							jQuery('.sf-select-box').selectpicker('refresh');

							}

							

						}



					});

			  }

		})
	.on('hidden.bs.modal', function(e) {
		if(currentclass){
			jQuery('body').addClass('modal-open');
		}else{
			jQuery('body').removeClass('modal-open');
		}
	});

	});

	

	jQuery('body').on('click', '.completebooking', function(){

	var currentclass = jQuery('body').hasClass('modal-open');
	
	var bid = jQuery(this).attr('data-id');														  

	bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

			  var data = {

			  "action": "complete_booking",

			  "bookingid": bid,

			};

			

			var data = jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						data: data,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.loading-area').show();

							jQuery('.alert').remove();

						},



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['role'] == 'provider' || data['role'] == 'administrator'){

							jQuery('#booking-details').addClass('hidden fade in');

							jQuery('#bookings-grid_wrapper').removeClass('hidden');

							dataTable.ajax.reload(null, false);	
							
							jQuery('.sf-select-box').selectpicker('refresh');

							}else{

							jQuery('#booking-details').addClass('hidden fade in');
							
							jQuery('#pastbooking-details').addClass('hidden fade in');

							jQuery('#upcomingbookings-customer-grid_wrapper').removeClass('hidden');
							
							jQuery('#pastbookings-customer-grid_wrapper').removeClass('hidden');

							CustomerPastdataTable.ajax.reload(null, false);
							
							CustomerUpcomingdataTable.ajax.reload(null, false);
							
							jQuery('.sf-select-box').selectpicker('refresh');

							}

						}



					});

			  }

		})
	.on('hidden.bs.modal', function(e) {
		if(currentclass){
			jQuery('body').addClass('modal-open');
		}else{
			jQuery('body').removeClass('modal-open');
		}
	});

	});

	

	jQuery('body').on('click', '.change_service_status', function(){
	var currentclass = jQuery('body').hasClass('modal-open');
	
	var $this = jQuery(this);
	
	var bsid = jQuery(this).data('bsid');

	var currentstatus = jQuery(this).data('currentstatus');
	
	if(currentstatus == 'completed')
	{
		var suremsg = param.change_incomplete_status;	
		var updatedstatus = 'pending';
	}else if(currentstatus == 'pending')
	{
		var suremsg = param.change_complete_status;
		var updatedstatus = 'completed';
	}

	bootbox.confirm(suremsg, function(result) {

		  if(result){

			  var data = {

			  "action": "change_bookedservice_status",

			  "bookedserviceid": bsid,

			  "currentstatus": currentstatus,

			};

			

			var data = jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						data: data,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.loading-area').show();

							jQuery('.alert').remove();

						},



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){

							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#booking-details" );	

							jQuery('#service-'+bsid+' .servicestatus').html(data['servicestatus']);
							
							$this.data('currentstatus',updatedstatus);

							}

							

						}



					});

			  }

		})
	.on('hidden.bs.modal', function(e) {
		if(currentclass){
			jQuery('body').addClass('modal-open');
		}else{
			jQuery('body').removeClass('modal-open');
		}
	});

	});

	

	jQuery('body').on('click', '.completebookingandpay', function(){
	
	var currentclass = jQuery('body').hasClass('modal-open');
	
	var bid = jQuery(this).attr('data-id');														  

	var amount = jQuery(this).data('amount');

	var providerid = jQuery(this).data('providerid');

	

	bootbox.confirm(param.complete_booking_and_pay, function(result) {

		  if(result){

			  var data = {

			  "action": "complete_booking_and_pay",

			  "bookingid": bid,

			  "providerid": providerid, 

			  "amount": amount 

			};

			

			var data = jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						data: data,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.alert').remove();

							jQuery('.loading-area').show();

						},



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['role'] == 'provider' || data['role'] == 'administrator'){

							jQuery('#booking-details').addClass('hidden fade in');

							jQuery('#bookings-grid_wrapper').removeClass('hidden');

							dataTable.ajax.reload(null, false);	
							
							jQuery('.sf-select-box').selectpicker('refresh');

							}else{

							jQuery('#booking-details').addClass('hidden fade in');
							
							jQuery('#pastbooking-details').addClass('hidden fade in');

							jQuery('#upcomingbookings-customer-grid_wrapper').removeClass('hidden');
							
							jQuery('#pastbookings-customer-grid_wrapper').removeClass('hidden');

							CustomerPastdataTable.ajax.reload(null, false);
							
							CustomerUpcomingdataTable.ajax.reload(null, false);
							
							jQuery('.sf-select-box').selectpicker('refresh');

							}

							if(data['status'] == 'success'){

								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#upcomingbookings-customer-grid_wrapper" );

								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#pastbookings-customer-grid_wrapper" );

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#upcomingbookings-customer-grid_wrapper" );

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#pastbookings-customer-grid_wrapper" );

							}

						}



					});

			  }

		})
	.on('hidden.bs.modal', function(e) {
		if(currentclass){
			jQuery('body').addClass('modal-open');
		}else{
			jQuery('body').removeClass('modal-open');
		}
	});

	});

	

	jQuery('body').on('click', '.completebookingandpayviastripe', function(){

	var currentclass = jQuery('body').hasClass('modal-open');
	
	var bid = jQuery(this).attr('data-id');														  

	var amount = jQuery(this).data('amount');

	var providerid = jQuery(this).data('providerid');

	

	bootbox.confirm(param.complete_booking_and_pay, function(result) {

		  if(result){

			  var data = {

			  "action": "complete_booking_and_pay_via_stripe",

			  "bookingid": bid,

			  "providerid": providerid, 

			  "amount": amount 

			};

			

			var data = jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						data: data,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.alert').remove();

							jQuery('.loading-area').show();

						},



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['role'] == 'provider' || data['role'] == 'administrator'){

							jQuery('#booking-details').addClass('hidden fade in');

							jQuery('#bookings-grid_wrapper').removeClass('hidden');

							dataTable.ajax.reload(null, false);	
							
							jQuery('.sf-select-box').selectpicker('refresh');

							}else{

							jQuery('#booking-details').addClass('hidden fade in');
							
							jQuery('#pastbooking-details').addClass('hidden fade in');

							jQuery('#upcomingbookings-customer-grid_wrapper').removeClass('hidden');
							
							jQuery('#pastbookings-customer-grid_wrapper').removeClass('hidden');

							CustomerPastdataTable.ajax.reload(null, false);
							
							CustomerUpcomingdataTable.ajax.reload(null, false);
							
							jQuery('.sf-select-box').selectpicker('refresh');

							}

							if(data['status'] == 'success'){

								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#upcomingbookings-customer-grid_wrapper" );

								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#pastbookings-customer-grid_wrapper" );
								
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#bookings-grid_wrapper" );

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#upcomingbookings-customer-grid_wrapper" );

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#pastbookings-customer-grid_wrapper" );
								
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#bookings-grid_wrapper" );

							}

						}



					});

			  }

		})
	.on('hidden.bs.modal', function(e) {
		if(currentclass){
			jQuery('body').addClass('modal-open');
		}else{
			jQuery('body').removeClass('modal-open');
		}
	});

	});

	

	//Change Status

	jQuery('body').on('click', '.changeStatus', function(){

		var currentclass = jQuery('body').hasClass('modal-open');										  

		var bid = jQuery(this).attr('data-id');

		

		bootbox.confirm(param.change_complete_status, function(result) {

		  if(result){

			  var data = {

			  "action": "change_status",

			  "bookingid": bid,

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

							dataTable.ajax.reload(null, false);
							
							jQuery('.sf-select-box').selectpicker('refresh');

						}



					});

			  }

		})
		.on('hidden.bs.modal', function(e) {
		if(currentclass){
			jQuery('body').addClass('modal-open');
		}else{
			jQuery('body').removeClass('modal-open');
		}
	});

		

    });

	

	//Comment Rating

	 jQuery("#comment-rating").rating({

            starCaptions: function(val) {

                if (val < 3) {

                    return val;

                } else {

                    return 'high';

                }

            },

            starCaptionClasses: function(val) {

                if (val < 3) {

                    return 'label label-danger';

                } else {

                    return 'label label-success';

                }

            },

            hoverOnClear: false

        });

	 

	 jQuery('#rating-input').rating({

              min: 0,

              max: 5,

              step: 1,

              size: 'lg',

              showClear: false

           });				  

				  

	/*Save Feeback*/				  

	jQuery('.add-feedback')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				'comment-rating': {

					validators: {

						notEmpty: {

							message: param.rating

						},

						greaterThan: {

							value: 0,

							message: param.rating

						},

					}

				},

				comment: {

					validators: {

						notEmpty: {

							message: param.comment

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

			if(jQuery("#comment-rating").val()==0){

				ratingflag = 1;

				jQuery('.rating_bx small').first().show(); 

				jQuery('.rating_bx').addClass('has-error').removeClass('has-success'); 

				jQuery('form.add-feedback').find('input[type="submit"]').prop('disabled', false);

				}else{

				ratingflag = 0;

				jQuery('.rating_bx small').first().hide(); 

				jQuery('.rating_bx').removeClass('has-error').addClass('has-success'); 

				jQuery('form.add-feedback').find('input[type="submit"]').prop('disabled', false);

				}

			

			

			

			if(ratingflag==1){form.preventDefault();return false;}

			form.preventDefault();

			

            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			rating = jQuery("#comment-rating").val();

			

			var data = {

			  "action": "add_feedback",

			  "booking_id": feedbookingid,

			  "rating":rating,

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

							

							if(data['status'] == 'success'){

								jQuery("#comment").val('');

								/*Close the popup window*/

								$form.parents('.bootbox').modal('hide');

								

								CustomerPastdataTable.ajax.reload(null, false);

								CustomerUpcomingdataTable.ajax.reload(null, false);
								
								jQuery('.sf-select-box').selectpicker('refresh');

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-feedback" );

							}

							

							

						

						}



					});

			

        });

  	

	/*Start Provider Booking Table*/

	//Tabbing on My Account Page

	jQuery('body').on('click','#myTab a',function(e){

		e.preventDefault();

		jQuery(this).tab('show');

		var tabid = jQuery(this).attr('href');

		

		load_booking_datatable(tabid);

	});

	

	function load_booking_datatable(tabid){

		if(tabid == '#bookings'){

			if ( ! jQuery.fn.DataTable.isDataTable( '#bookings-grid' ) ) {	

			dataTable = jQuery('#bookings-grid').DataTable( {

	"serverSide": true,

	"bAutoWidth": false,

	"order": [[ 1, "desc" ]],

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

		data: {"action": "get_bookings","user_id": user_id},

		error: function(){  // error handling

			jQuery(".bookings-grid-error").html("");

			jQuery("#bookings-grid").append('<tbody class="bookings-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#bookings-grid_processing").css("display","none");

			

		}

	},
	 drawCallback: function(){
          jQuery('.display-ratings').rating();
		  jQuery('.sf-show-rating').show();	
		  jQuery('.paginate_button', this.api().table().container())          
             .on('click', function(){
				jQuery('.sf-couponcode-popup-overlay').fadeIn();
             });       
       }

	} );

			}

		}	

	}

	
jQuery('#bookings-grid').on('draw.dt', function() {
	jQuery('.sf-couponcode-popup-overlay').fadeOut();
	jQuery('[data-toggle="tooltip"]').tooltip(); 
	jQuery("[data-toggle=popover]").each(function() {

		  jQuery(this).popover({

			html: true,

			content: function() {

		   var id = jQuery(this).attr('id')

		   return jQuery('#popover-content-' + id).html();

			}

		  });

		 });
	jQuery('.sf-select-box').selectpicker('refresh');
});
	

	

	//Bulk Bookings Delete

	jQuery("#bulkBookingsDelete").on('click',function() { // bulk checked

        var status = this.checked;

        jQuery(".deleteBookingRow").each( function() {

            jQuery(this).prop("checked",status);

        });

    });

	

	

     

    //Single Booking Delete

	jQuery('#deleteBookingTriger').on("click", function(event){ // triggering delete one by one

        

			  if( jQuery('.deleteBookingRow:checked').length > 0 ){

				  bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

				  // at-least one checkbox checked

            var ids = [];

            jQuery('.deleteBookingRow').each(function(){

                if(jQuery(this).is(':checked')) { 

                    ids.push(jQuery(this).val());

                }

            });

            var ids_string = ids.toString();  // array to string conversion 

            jQuery.ajax({

                type: "POST",

                url: ajaxurl,

                data: {action: "delete_bookings", data_ids:ids_string},

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

	/*End Provider Booking Table*/

	

	/*Assign Member or Booking*/

     jQuery('.assign-member')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				memberid: {

					validators: {

						notEmpty: {

							message: param.any_member

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

			  "action": "assign_new_member"

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

								

								/*Close the popup window*/

								// Hide the dialog

				                $form.parents('.bootbox').modal('hide');

								

								/*Reaload datatable after add new member*/

								dataTable.ajax.reload(null, false);
								
								jQuery('.sf-select-box').selectpicker('refresh');

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.assign-member" );

							}

							

							

						

						}



					});

			

        });

	

	/*Update Booking date and member id*/

     jQuery('.edit-booking')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				memberid: {

					validators: {

						notEmpty: {

							message: param.any_member

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



			var getslot = jQuery("#boking-slot").val();

			var date = jQuery('#date').val();

			var memberid = jQuery('#memberid').val();

			

			if(date == ""){

				jQuery( "<div class='alert alert-danger'>"+param.select_date+"</div>" ).insertBefore( "form.edit-booking" );

				$form.find('input[type="submit"]').prop('disabled', false);

				return false;

			}

			

			if(getslot == "" && memberid == ""){

				jQuery( "<div class='alert alert-danger'>"+param.timeslot_member+"</div>" ).insertBefore( "form.edit-booking" );

				$form.find('input[type="submit"]').prop('disabled', false);

				return false;

			}

			

            

			

			var data = {

			  "action": "update_booking"

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

								

								/*Close the popup window*/

								// Hide the dialog

				                $form.parents('.bootbox').modal('hide');

								

								/*Reaload datatable after add new member*/

								CustomerUpcomingdataTable.ajax.reload(null, false);
								
								jQuery('.sf-select-box').selectpicker('refresh');

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-booking" );

							}

							

							

						

						}



					});

			

        });

	

	/*Start Past Customer Booking Table*/

	//Display Bookings in Data Table

	CustomerPastdataTable = jQuery('#pastbookings-customer-grid').DataTable( {

	"paging":   false,

	"ordering": false,

    "info":     false,

	"searching": false,

	"bAutoWidth": false,

	"serverSide": true,

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

		data: {"action": "get_customer_pastbookings"},

		error: function(){  // error handling

			jQuery(".pastbookings-customer-grid-error").html("");

			jQuery("#pastbookings-customer-grid").append('<tbody class="pastbookings-customer-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#pastbookings-customer-grid_processing").css("display","none");

			

		},

		

	},
	drawCallback: function(){
          jQuery('.display-ratings').rating();
		  jQuery('.sf-show-rating').show();	
		  jQuery('.paginate_button', this.api().table().container())          
             .on('click', function(){
				jQuery('.sf-couponcode-popup-overlay').fadeIn();
             });       
       }

	} );
	
	jQuery('#pastbookings-customer-grid').on('draw.dt', function() {
		jQuery('.sf-couponcode-popup-overlay').fadeOut();
		jQuery('.sf-select-box').selectpicker('refresh');
		jQuery('[data-toggle="tooltip"]').tooltip(); 
		jQuery("[data-toggle=popover]").each(function() {

		  jQuery(this).popover({

			html: true,

			content: function() {

		   var id = jQuery(this).attr('id')

		   return jQuery('#popover-content-' + id).html();

			}

		  });

		 });
	});


	/*End Customer Booking Table*/

	

	/*Start Upcoming Customer Booking Table*/

	//Display Bookings in Data Table

	CustomerUpcomingdataTable = jQuery('#upcomingbookings-customer-grid').DataTable( {

	"paging":   false,

	"ordering": false,

	"bAutoWidth": false,

	"searching": false,

    "info":     false,																						

	"serverSide": true,

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

		data: {"action": "get_customer_upcomingbookings"},

		error: function(){  // error handling

			jQuery(".upcomingbookings-customer-grid-error").html("");

			jQuery("#upcomingbookings-customer-grid").append('<tbody class="upcomingbookings-customer-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#upcomingbookings-customer-grid_processing").css("display","none");

			

		}

	},
	drawCallback: function(){
          jQuery('.display-ratings').rating();
		  jQuery('.sf-show-rating').show();	
		  jQuery('.paginate_button', this.api().table().container())          
             .on('click', function(){
				jQuery('.sf-couponcode-popup-overlay').fadeIn();
             });       
       }

	} );
	
	jQuery('#upcomingbookings-customer-grid').on('draw.dt', function() {
		jQuery('.sf-couponcode-popup-overlay').fadeOut();
		jQuery('.sf-select-box').selectpicker('refresh');
		jQuery('[data-toggle="tooltip"]').tooltip(); 
		jQuery("[data-toggle=popover]").each(function() {

		  jQuery(this).popover({

			html: true,

			content: function() {

		   var id = jQuery(this).attr('id')

		   return jQuery('#popover-content-' + id).html();

			}

		  });

		 });
	});

	/*End Customer Booking Table*/

	

	/*Add Invoice*/

	

	var servicetotal = 0;

	var discount = 0;

	var tax = 0;

	var total = 0;

	var grand_total = 0;

	var temprice = 0;

	var bookingid = '';

	var bookingemail = '';

	var serviceEditIndex = '';

	var formID = '';

	

					  

	jQuery('body').on('click', '.addInvoice', function(){

												

	 bookingid = jQuery(this).attr('data-id');

	 

	 bookingemail = jQuery(this).attr('data-email');

	 

	 formID = 'addBookingInvoice';

	  

	  							jQuery('#addBookingInvoice')

								.find('[name="customer"]').val(bookingemail).end();

	  							jQuery('.sf-select-box').selectpicker('refresh');

								

								jQuery("#editservicedata").find('.num-hours').find('input[type="text"]').TouchSpin({

								  verticalbuttons: true,

								  verticalupclass: 'glyphicon glyphicon-plus',

								  verticaldownclass: 'glyphicon glyphicon-minus',

								   min: 1,

									max: 12

								});

							// Show the dialog

							bootbox

								.dialog({

									title: param.add_invoice,

									message: jQuery('#addBookingInvoice'),

									 onEscape: function() { jQuery('#addBookingInvoice').hide().appendTo('body'); },

									show: false // We will show it manually later

								})

								.on('shown.bs.modal', function() {

									jQuery('#addBookingInvoice')

										.show()                             // Show the login form

										.bootstrapValidator('resetForm'); // Reset form

									jQuery('.bootbox').find('.modal-dialog').addClass('modal-xlg');

									jQuery('.bootbox').find('.modal-body').addClass('clearfix row');											

								})

								.on('hide.bs.modal', function(e) {

									servicetotal = 0;

									discount = 0;

									tax = 0;

									total = 0;

									grand_total = 0;

									jQuery('.add-booking-invoice input[name="refno"]').val('');

									jQuery('.add-booking-invoice input[name="dueDate"]').val('');

									jQuery('.add-booking-invoice select[name="customer"]').val('');

									jQuery('.add-booking-invoice select[name="status"]').val('');

									jQuery('.add-booking-invoice input[name="discount"]').val('');

									jQuery('.add-booking-invoice input[name="tax"]').val('');

									jQuery('.add-booking-invoice #total_amount').html(currencysymbol+'0.00');

									jQuery('.add-booking-invoice #total_discount').html(currencysymbol+'0.00');

									jQuery('.add-booking-invoice #total_tax').html(currencysymbol+'0.00');

									jQuery('.add-booking-invoice #grand_total').html(currencysymbol+'0.00');

									jQuery('.add-booking-invoice textarea[name="short-desc"]').val('');

									

									jQuery('.add-booking-invoice select[name="service_title[0]"]').val('');

									jQuery('.add-booking-invoice input[name="service_desc[0]"]').val('');

									jQuery('.add-booking-invoice input[name="service_price[0]"]').val('');

									jQuery('.add-booking-invoice .additional-element').remove();

									jQuery('.add-booking-invoice .num-hours').hide('');

									

									jQuery('.sf-select-box').selectpicker('refresh');															  

									// Bootbox will remove the modal (including the body which contains the login form)

									// after hiding the modal

									// Therefor, we need to backup the form

									jQuery('#addBookingInvoice').hide().appendTo('body');

								})

								.modal('show');											

	

       										   

	});			  

	

	jQuery("input[name='num_hours[0]']").TouchSpin({

      verticalbuttons: true,

      verticalupclass: 'glyphicon glyphicon-plus',

      verticaldownclass: 'glyphicon glyphicon-minus',

	   min: 1,

        max: 12

    });	

	



				jQuery('body').on('click','.gen_ref',function(e){

					jQuery(this).parent('.input-group').children('input').val(service_finder_getRandomRef());

				});

				function service_finder_getRandomRef(){var min=1000000000000000,max=9999999999999999;return Math.floor(Math.random()*(max- min+ 1))+ min;}

				

				var date = new Date();

                date.setDate(date.getDate()+1);

				

				jQuery('.dueDatePicker').datepicker({

					 format: 'yyyy/mm/dd',													

					 startDate: date,
					 
					 language: langcode

				})

				.on('changeDate', function(evt) {

					// Revalidate the date field

					

					jQuery('.add-booking-invoice').bootstrapValidator('revalidateField', jQuery(this).find('[name="dueDate"]'));

				}).on('hide', function(event) {
				event.preventDefault();
				event.stopPropagation();
				});

				

	jQuery('.form-group').on('click', 'input[type=radio]', function(){

		service_finder_calculateBookingTotal(formID);

	});

	

	jQuery('.form-group').on('change', 'input[name=tax]', function(){

		service_finder_calculateBookingTotal(formID);

	});

	

	jQuery('.form-group').on('change', 'input[name=discount]', function(){

		service_finder_calculateBookingTotal(formID);

	});

	

	jQuery('.form-group').on('change', '.col-xs-2 input[type=text]', function(){

		service_finder_calculateBookingTotal(formID);

	});

	

	

	var descValidators = {

            row: '.col-xs-3',   // The title is placed inside a <div class="col-xs-4"> element

            validators: {

                notEmpty: {

                    message: param.desc_req

                }

            }

        };

     var priceValidators = {

            row: '.col-xs-2',

            validators: {

                notEmpty: {

                    message: param.price

                },

            }

        };

    

	var serviceIndex = 0;

   	jQuery('.add-booking-invoice')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				refno: {
					validators: {
						notEmpty: {
						}
					}
				},

				dueDate: {

                    validators: {

                        notEmpty: {

                            message: param.due_date

                        },

                    }

                },

				'service_desc[0]': descValidators,

                'service_price[0]': priceValidators

            }

        })

		.on('click',  'input[type="submit"]', function(e) {

            if(jQuery('.add-booking-invoice select[name="customer"] option:selected').val()==""){customer_flag = 1;jQuery('.add-booking-invoice select[name="customer"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.add-booking-invoice').find('input[type="submit"]').prop('disabled', false);}else{customer_flag = 0;jQuery('.add-booking-invoice select[name="customer"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.add-booking-invoice').find('input[type="submit"]').prop('disabled', false);}

			 if(jQuery('.add-booking-invoice select[name="status"] option:selected').val()==""){status_flag = 1;jQuery('.add-booking-invoice select[name="status"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.add-booking-invoice').find('input[type="submit"]').prop('disabled', false);}else{status_flag = 0;jQuery('.add-booking-invoice select[name="status"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.add-booking-invoice').find('input[type="submit"]').prop('disabled', false);}

	    })

		.on('error.field.bv', function(e, data) {

            data.bv.disableSubmitButtons(false); // disable submit buttons on errors

	    })

		.on('status.field.bv', function(e, data) {

            data.bv.disableSubmitButtons(false); // disable submit buttons on valid

        })

		// Add button click handler

        .on('click', '.addButton', function() {

			serviceIndex++;

            var $template = jQuery('#serviceBookingTemplate'),

                $clone    = $template

                                .clone()

								.addClass('additional-element')

                                .removeClass('hide')

                                .removeAttr('id')

                                .attr('data-service-index', serviceIndex)

                                .insertBefore($template);

								$clone.find('.bootstrap-select').remove();

        $clone.find('select').selectpicker().change(function(e) {

                

				var sid = jQuery(this).val();

				var indexVal = jQuery(this).attr('data-index');

				var data = {

					  "action": "getServiceDetails",

					  "serviceid": sid,

					};

					

				  var formdata = jQuery.param(data);

				  

				  jQuery.ajax({

			

									type: 'POST',

			

									url: ajaxurl,

			

									data: formdata,

									

									dataType: "json",

									

									success:function (data, textStatus) {

										

										if(data['status'] == 'success'){

											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);

											if(data['cost_type'] == 'hourly'){

												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);

												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();

											}else if(data['cost_type'] == 'fixed'){

												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);

												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();

											}

											

											service_finder_calculateBookingTotal(formID);

											

										}

		

									}

			

								});

				

            });

		

		$clone.find('[type="radio"]').change(function(e) {

			var ctype = jQuery(this).val();

			var indexVal = jQuery(this).attr('data-index');

			if(ctype == 'hourly'){

				jQuery(this).closest('div.form-group').find('.num-hours').show();

			}else{

				jQuery(this).closest('div.form-group').find('.num-hours').hide();

			}

		});

		

		

		  

		   $clone.find('.col-xs-2 input[type=text]').change(function(e) {

					service_finder_calculateBookingTotal(formID);											

			});

		   

		   



            // Update the name attributes

            $clone

                .find('[name="service_title"]').attr('name', 'service_title[' + serviceIndex + ']').attr('data-index', serviceIndex ).end()

                .find('[name="cost_type"][value="fix"]').attr('name', 'cost_type[' + serviceIndex + ']').attr('data-index', serviceIndex ).attr('id', 'fix-booking-price[' + serviceIndex + ']' ).append('<label for="fix-booking-price[' + serviceIndex + ']">Fix</label>').end()

				.find('[name="cost_type"][value="hourly"]').attr('name', 'cost_type[' + serviceIndex + ']').attr('data-index', serviceIndex ).attr('id', 'hourly-booking-price[' + serviceIndex + ']' ).end()

				.find('label[for="fix-booking-price"]').attr('for', 'fix-booking-price[' + serviceIndex + ']').end()

				.find('label[for="hourly-booking-price"]').attr('for', 'hourly-booking-price[' + serviceIndex + ']').end()

				.find('[name="num_hours"]').attr('name', 'num_hours[' + serviceIndex + ']').attr('data-index', serviceIndex ).end()

                .find('[name="service_desc"]').attr('name', 'service_desc[' + serviceIndex + ']').attr('data-index', serviceIndex ).end()

				.find('[name="service_price"]').attr('name', 'service_price[' + serviceIndex + ']').attr('data-index', serviceIndex ).end();

				

				 $clone.find('.num_hours2').TouchSpin({

                verticalbuttons: true,

      verticalupclass: 'glyphicon glyphicon-plus',

      verticaldownclass: 'glyphicon glyphicon-minus',

	  min: 1,

        max: 12

            }).on('change', function() {

            // Revalidate the date field

				var nmhrs = jQuery(this).val();

				var indexVal = jQuery(this).attr('data-index');						

				temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						

				service_finder_calculateBookingTotal(formID);

        });



            // Add new fields

            // Note that we also pass the validator rules for new field as the third parameter

            jQuery('.add-invoice')

                .bootstrapValidator('addField', 'service_desc[' + serviceIndex + ']', descValidators)

				.bootstrapValidator('addField', 'service_price[' + serviceIndex + ']', priceValidators);

        })

		.on('change', '[name="cost_type[0]"]', function() {

			var ctype = jQuery(this).val();

			var indexVal = jQuery(this).attr('data-index');

			if(ctype == 'hourly'){

				jQuery(this).closest('div.form-group').find('.num-hours').show();

			}else{

				jQuery(this).closest('div.form-group').find('.num-hours').hide();

			}

		})

		.on('change', '[name="service_title[0]"]', function() {

				var sid = jQuery(this).val();

				var indexVal = jQuery(this).attr('data-index');

				var data = {

					  "action": "getServiceDetails",

					  "serviceid": sid,

					};

					

				  var formdata = jQuery.param(data);

				  

				  jQuery.ajax({

			

									type: 'POST',

			

									url: ajaxurl,

			

									data: formdata,

									

									dataType: "json",

									

									success:function (data, textStatus) {

										

										if(data['status'] == 'success'){

											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);

											

											if(data['cost_type'] == 'hourly'){

												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);

												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();

											}else if(data['cost_type'] == 'fixed'){

												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);

												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();

											}

											

											

											service_finder_calculateBookingTotal(formID);

											

										}

		

									}

			

								});							   

		})

		

		.on('change', '[name="num_hours[0]"]', function() {

																		

				var nmhrs = jQuery(this).val();

				var indexVal = jQuery(this).attr('data-index');						

				temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						

				service_finder_calculateBookingTotal(formID);

		})

		

		// Remove button click handler

        .on('click', '.removeButton', function() {

            var $row  = jQuery(this).parents('.form-group'),

                index = $row.attr('data-service-index');



            // Remove fields

            jQuery('.add-invoice')

                .bootstrapValidator('removeField', $row.find('[name="service_desc[' + index + ']"]'))

                .bootstrapValidator('removeField', $row.find('[name="service_price[' + index + ']"]'));



            // Remove element containing the fields

            $row.remove();

			service_finder_calculateBookingTotal(formID);

        })

        .on('success.form.bv', function(form) {

            jQuery('form.add-booking-invoice').find('input[type="submit"]').prop('disabled', false);

			// Prevent form submission

            form.preventDefault();

			if(customer_flag==1 || status_flag==1){return false;}

			jQuery('#serviceBookingTemplate').remove();

			// Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			var data = {

			  "action": "add_booking_invoice",

			  "bookingid": bookingid,

			  "total": total,

			  "gtotal": grand_total,

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

								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-booking-invoice" );	

								window.setTimeout(function(){

									/*Close the popup window*/

									$form.parents('.bootbox').modal('hide');

								}, 1000); // 1 seconds expressed in milliseconds

								/*Reaload invoicedataTable after add new service*/

								invoicedataTable.ajax.reload(null, false);
								
								jQuery('.sf-select-box').selectpicker('refresh');

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-booking-invoice" );

							}

							

						}



					});

			

        });

		

		function service_finder_calculateBookingTotal(formid){

			var taxtype = jQuery('#'+formid).find('input[name=tax-type]:checked').val();

			var discounttype = jQuery('#'+formid).find('input[name=discount-type]:checked').val();

			

			var discountval = jQuery('#'+formid).find('input[name=discount]').val();

			var taxval = jQuery('#'+formid).find('input[name=tax]').val();

			servicetotal = 0;

			

			

			jQuery('#'+formid).find('.col-xs-2 input[type=text]').each(function(){

				   var price = parseFloat(jQuery(this).val());

				   var hrs = jQuery(this).closest('div.form-group').find('.num-hours').find('input').val();

				   var type = jQuery(this).closest('div.form-group').find('input[type=radio]:checked').val();

				   if(type == 'hourly' && price > 0){

					   servicetotal = parseFloat(servicetotal) + (parseFloat(price) * parseFloat(hrs));	

				   }else if(price > 0){

					   servicetotal = parseFloat(servicetotal) + parseFloat(price);	

				   }

				   

			})



			total = servicetotal;

			

			if(discounttype == 'fix'){

				if(discountval > 0){

					discount = parseFloat(discountval);

				}else{
					
					discount = 0;	
				}

			}else if(discounttype == 'percentage'){

				if(discountval > 0){

					discount = (parseFloat(discountval)/100) * parseFloat(total);

				}else{
					
					discount = 0;	
				}

			}

			

			if(taxtype == 'fix'){

				

				if(taxval > 0){

					tax = parseFloat(taxval);

				}else{
					
					tax = 0;	
				}

			}else if(taxtype == 'percentage'){

				if(taxval > 0){

					tax = (parseFloat(taxval)/100) * parseFloat(total);

				}else{
					
					tax = 0;	
				}

			}

			

			grand_total = (parseFloat(total) - parseFloat(discount)) + parseFloat(tax);



			jQuery('#'+formid).find('#total_discount').html(currencysymbol+discount.toFixed(2));

			jQuery('#'+formid).find('#total_tax').html(currencysymbol+tax.toFixed(2));

			jQuery('#'+formid).find('#total_amount').html(currencysymbol+total.toFixed(2));

			jQuery('#'+formid).find('#grand_total').html(currencysymbol+grand_total.toFixed(2));

		}

		

	

  

	/*End Add Invoice*/

	

	

  });

  

  /*Timeslot callback function*/

  function service_finder_timeslotCallback(id, provider_id, totalhours, bookingid) {

	    if (totalhours === undefined) {

			  totalhours = 0;

		} 

		

		if (bookingid === undefined) {

			  bookingid = 0;

		} 

	  

	  	service_finder_resetMembers();

		var date = jQuery("#" + id).data("date");

		jQuery('#selecteddate').attr('data-seldate',date);

		var data = {

			  "action": "get_bookingtimeslot",

			  "seldate": date,

			  "provider_id": provider_id,

			  "totalhours": totalhours,

			  "editbooking": 'yes',

			  "bookingid": bookingid,

			};

		var formdata = jQuery.param(data);

		  

		jQuery.ajax({



			type: 'POST',



			url: ajaxurl,



			data: formdata,

			

			beforeSend: function() {

				jQuery('.loading-area').show();

			},



			success:function (data, textStatus) {

				jQuery('.loading-area').hide();

				jQuery('.timeslots').html(data);

			}



		});



		  return true;

	}

	

	/*Member callback function*/

	function service_finder_memberCallback(id, provider_id, zipcode) {

	  	service_finder_resetMembers();

		var zipcode = jQuery('input[name="zipcode"]').val();

		var provider_id = jQuery('#provider').attr('data-provider');

		var date = jQuery("#" + id).data("date");

		

		var data = {

			  "action": "load_members",

			  "zipcode": zipcode,

			  "provider_id": provider_id,

			  "date": date,

			  'customeredit': 'yes',

			  'bookingid': bookingid

			};

		var formdata = jQuery.param(data);

		  

		jQuery.ajax({



			type: 'POST',



			url: ajaxurl,



			data: formdata,

			

			dataType: "json",

			

			beforeSend: function() {

				jQuery('.loading-area').show();

			},



			success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							jQuery("#panel-2").find(".alert").remove();

							 if(data != null){

								if(data['status'] == 'success'){

									jQuery("#panel-2").find("#members").html(data['members']);

									jQuery("#panel-2").find("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>');

									jQuery('.display-ratings').rating();

									jQuery('.sf-show-rating').show();

								}

							}

					}



		});	



		  return true;

	}	

	

	/*Reset member function*/

	function service_finder_resetMembers(){

		jQuery("#editbooking").find("#members").html('');

		jQuery("#memberid").val('');	

	}