// JavaScript Document

jQuery(document).ready(function() {

'use strict';								



	jQuery('[data-toggle="tooltip"]').tooltip();

	

	jQuery('#job-applicants-listing').on('show.bs.modal', function (event) {

	   jQuery('#job-applicants-listing .modal-body .listing-grid-box .row').html('');																	



	   var button = jQuery(event.relatedTarget);

	   var jobid = button.data('jobid');

		

		var data = {

			  "action": "applicants_listing",

			  "jobid": jobid

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

			jQuery('form.applyforjob').find('input[type="submit"]').prop('disabled', false);	

			if(data['status'] == 'success'){

				jQuery('#job-applicants-listing .modal-body .listing-grid-box .row').html(data['applicants']);

				equalheight('.equal-col-outer .equal-col');

				jQuery('[data-toggle="tooltip"]').tooltip();

			}

		

		}

	});

  });	 


	

});// Document.ready END====================================================//



	var equalheight = function(container) {

		var currentTallest = 0,

			currentRowStart = 0,

			rowDivs = new Array(),

			$el,

			topPosition = 0;

		jQuery(container).each(function() {

	

			$el = jQuery(this);

			jQuery($el).height('auto')

			topPostion = $el.position().top;

	

			if (currentRowStart != topPostion) {

				for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {

					rowDivs[currentDiv].height(currentTallest);

				}

				rowDivs.length = 0; // empty the array

				currentRowStart = topPostion;

				currentTallest = $el.height();

				rowDivs.push($el);

			} else {

				rowDivs.push($el);

				currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);

			}

			for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {

				rowDivs[currentDiv].height(currentTallest);

			}

		});

	}

	

	jQuery(window).resize(function() {

		equalheight('.equal-col-outer .equal-col');

	});

