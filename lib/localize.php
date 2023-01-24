<?php

/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/

?>

<?php

$service_finder_options = get_option('service_finder_options');

$current_user = service_finder_plugin_global_vars('current_user');

$hire_if_booking_off_msg = (!empty($service_finder_options['hire-bookingoff'])) ? esc_attr($service_finder_options['hire-bookingoff']) : esc_html__( 'Provider booking form is closed. Still you want to book him?', 'service-finder' );



$minwalletamount = (!empty($service_finder_options['min-wallet-amount'])) ? esc_html($service_finder_options['min-wallet-amount']) : 0;

$maxwalletamount = (!empty($service_finder_options['max-wallet-amount'])) ? esc_html($service_finder_options['max-wallet-amount']) : 0;

$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Providers', 'service-finder');



$walleturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');

					

if(service_finder_getUserRole($current_user->ID) == 'Customer'){

$walleturl = add_query_arg( array('action' => 'wallet'), $walleturl );

}else{

$walleturl = add_query_arg( array('tabname' => 'wallet'), $walleturl );

}



$walletwaring = esc_html__( 'Insufficient wallet amount. Please %LINK_START%add balance%LINK_END% to wallet', 'service-finder' );

$walletwaring = str_replace('%LINK_START%','<a href="'.$walleturl.'" target="_blank">',$walletwaring);

$walletwaring = str_replace('%LINK_END%','</a>',$walletwaring);



$string_array = array(

	'not_valid' => esc_html__( 'This value is not valid', 'service-finder' ),

	'email_exist' => esc_html__( 'Email already exist', 'service-finder' ),

	'req' => esc_html__( 'This field is required', 'service-finder' ),

	'are_you_sure' => esc_html__( 'Are you sure?', 'service-finder' ),

	'are_you_sure_cancel_membership' => sprintf(esc_html__( 'Are you sure you want to cancel this %s membership?', 'service-finder' ),strtolower($providerreplacestring)),

	'are_you_sure_reset_business_hours' => sprintf(esc_html__( 'Are you sure you want to reset %s business hours?', 'service-finder' ),strtolower($providerreplacestring)),

	'are_you_sure_reactivate_membership' => sprintf(esc_html__( 'Are you sure you want to reactivate this %s membership?', 'service-finder' ),strtolower($providerreplacestring)),

	'bulk_slots_warning' => esc_html__( 'All existing slots will be deleted for selected days after this bulk import.', 'service-finder' ),

	'are_you_sure_approve_mail' => esc_html__( 'Are you sure? Mail will be sent to provider after approve autometically.', 'service-finder' ),

	'login_user_name' => esc_html__( 'Username is required', 'service-finder' ),

	'login_password' => esc_html__( 'Password is required', 'service-finder' ),

	'fp_user_login' => esc_html__( 'Please enter username or email', 'service-finder' ),

	'signup_user_name' => esc_html__( 'Username is required', 'service-finder' ),

	'signup_first_name' => esc_html__( 'First name is required', 'service-finder' ),

	'signup_last_name' => esc_html__( 'Last name is required', 'service-finder' ),

	'signup_address' => esc_html__( 'Address is required', 'service-finder' ),

	'allowed_country' => esc_html__( 'This country is not allowed', 'service-finder' ),

	'signup_city' => esc_html__( 'Please select city from suggestion', 'service-finder' ),

	'nationality_req' => esc_html__( 'Nationality is required', 'service-finder' ),

	'signup_country' => esc_html__( 'Country is required', 'service-finder' ),

	'signup_user_email' => esc_html__( 'The input is not a valid email address', 'service-finder' ),

	'providertermsncondition' => esc_html__( 'Please check the checkbox', 'service-finder' ),

	'signup_password_empty' => esc_html__( 'The password is required and cannot be empty', 'service-finder' ),

	'signup_password_length' => esc_html__( 'Password must be 5 to 15 characters long', 'service-finder' ),

	'signup_password_confirm' => esc_html__( 'The password and its confirm are not the same', 'service-finder' ),

	'primary_category' => esc_html__( 'Primary Category is required', 'service-finder' ),

	'category' => esc_html__( 'Category is required', 'service-finder' ),

	'min_cost' => esc_html__( 'Minimum cost is required', 'service-finder' ),

	'allowed_booking' => esc_html__( 'Number of bookings allowed', 'service-finder' ),

	'edit_unavl' => esc_html__( 'Edit UnAvailability', 'service-finder' ),

	'edit_offers' => esc_html__( 'Edit Offers', 'service-finder' ),

	'select_date' => esc_html__( 'Please select date', 'service-finder' ),

	'select_timeslot' => esc_html__( 'Please select timeslot or check the checkbox for wholeday', 'service-finder' ),

	'select_timeslot_req' => esc_html__( 'Please select timeslot', 'service-finder' ),

	'select_checkbox' => esc_html__( 'Please select at least one checkbox', 'service-finder' ),

	'region' => esc_html__( 'Region is required', 'service-finder' ),

	'change_status' => esc_html__( 'Are you sure you want to change the status?', 'service-finder' ),

	'no_data' => esc_html__( 'No data found in the server', 'service-finder' ),

	'postal_code' => esc_html__( 'Postal Code is required', 'service-finder' ),

	'edit_service' => esc_html__( 'Edit Service', 'service-finder' ),

	'edit_article' => esc_html__( 'Edit Article', 'service-finder' ),

	'edit_applied_job' => esc_html__( 'Edit Applied Job', 'service-finder' ),

	'view_applied_job' => esc_html__( 'View Applied Job', 'service-finder' ),

	'service_name' => esc_html__( 'Service name is required', 'service-finder' ),

	'select_payment' => esc_html__( 'Please select payment method', 'service-finder' ),

	'set_key' => esc_html__( 'Please set secret and publish key for stripe', 'service-finder' ),

	'pub_key' => esc_html__( 'You did not set a valid publishable key', 'service-finder' ),

	'change_complete_status' => esc_html__( 'Are you sure you want to change the status to complete?', 'service-finder' ),

	'change_incomplete_status' => esc_html__( 'Are you sure you want to change the status to incomplete?', 'service-finder' ),

	'member' => esc_html__( 'Member name is required', 'service-finder' ),

	'anyone' => esc_html__( 'Any One', 'service-finder' ),

	'assign_member' => esc_html__( 'Assign Member', 'service-finder' ),

	'add_feedback' => esc_html__( 'Add Feedback', 'service-finder' ),

	'feedback' => esc_html__( 'Feedback', 'service-finder' ),

	'edit_booking' => esc_html__( 'Edit Booking', 'service-finder' ),

	'rating' => esc_html__( 'Please give some rating', 'service-finder' ),

	'comment' => esc_html__( 'Please enter some comment', 'service-finder' ),

	'any_member' => esc_html__( 'Please select any member', 'service-finder' ),

	'timeslot_member' => esc_html__( 'Please select timeslot or member', 'service-finder' ),

	'add_invoice' => esc_html__( 'Add Invoice', 'service-finder' ),

	'desc_req' => esc_html__( 'Description is required', 'service-finder' ),

	'price' => esc_html__( 'The price is required', 'service-finder' ),

	'due_date' => esc_html__( 'The due date is required', 'service-finder' ),

	'edit_invoice' => esc_html__( 'Edit Invoice', 'service-finder' ),

	'reminder_mail' => esc_html__( 'Send Reminder Mail', 'service-finder' ),

	'comment_text' => esc_html__( 'Comments', 'service-finder' ),

	'cancel' => esc_html__( 'Cancel', 'service-finder' ),

	'cancel_sub' => esc_html__( 'Are you sure you want to cancel subscription?', 'service-finder' ),

	'edit_featured_price' => esc_html__( 'Edit Price', 'service-finder' ),

	'cancel_featured' => esc_html__( 'Are you sure you want to cancel featured/featured request?', 'service-finder' ),

	'customer_name' => esc_html__( 'Customer name is required', 'service-finder' ),

	'add_to_fav' => esc_html__( 'Add to Fav', 'service-finder' ),
	
	'add_to_favorite' => esc_html__( 'Add to Favorites', 'service-finder' ),

	'select_service' => esc_html__( 'Please select atleast one service', 'service-finder' ),

	'otp_mail' => esc_html__( 'Please check email for OTP', 'service-finder' ),

	'otp_pass' => esc_html__( 'Please enter your OTP', 'service-finder' ),

	'otp_right' => esc_html__( 'Please insert correct otp', 'service-finder' ),

	'reconfirm_email' => esc_html__( 'Please re-confirm the email address', 'service-finder' ),

	'gen_otp' => esc_html__( 'Generate One time Password to Confirm Email', 'service-finder' ),

	'edit_text' => esc_html__( 'Edit', 'service-finder' ),

	'state' => esc_html__( 'State is required', 'service-finder' ),

	'city' => esc_html__( 'City is required', 'service-finder' ),

	'service_not_avl' => esc_html__( 'Service not available in your area', 'service-finder' ),

	'notavl_select_service' => esc_html__( 'Service not available in your area or Select atleast one service', 'service-finder' ),

	'region_and_service' => esc_html__( 'Please select region and at least one service', 'service-finder' ),

	'timeslot' => esc_html__( 'Please select timeslot', 'service-finder' ),

	'member_select' => esc_html__( 'Please select member', 'service-finder' ),

	'my_fav' => esc_html__( 'My Favorite', 'service-finder' ),

	'booking_suc' => esc_html__( 'Congratulations! Your booking has been made successfully', 'service-finder' ),

	'postcode_not_avl' => esc_html__( 'Postal Code is not available', 'service-finder' ),

	'submit_now' => esc_html__( 'Submit Now', 'service-finder' ),

	'next_text' => esc_html__( 'Next', 'service-finder' ),

	'paynow' => esc_html__( 'Pay Now', 'service-finder' ),

	'dt_first' => esc_html__( 'First', 'service-finder' ),

	'dt_last' => esc_html__( 'Last', 'service-finder' ),

	'dt_previous' => esc_html__( 'Prev', 'service-finder' ),

	'dt_next' => esc_html__( 'Next', 'service-finder' ),

	'dt_search' => esc_html__( 'Search', 'service-finder' ),

	'dt_show' => esc_html__( 'Show', 'service-finder' ),

	'dt_entries' => esc_html__( 'entries', 'service-finder' ),

	'dt_showing' => esc_html__( 'Showing', 'service-finder' ),

	'dt_to' => esc_html__( 'to', 'service-finder' ),

	'dt_of' => esc_html__( 'of', 'service-finder' ),

	'lang' => str_replace('_','-',get_locale()),

	'select_timeslot' => esc_html__( 'Select Timeslot', 'service-finder' ),

	'hire_if_booking_off_msg' => $hire_if_booking_off_msg,

	'select_plan' => esc_html__( 'Please select plan', 'service-finder' ),

	'add_city' => esc_html__( 'Add new city', 'service-finder' ),

	'latlng_notfound' => esc_html__( 'Lat and long cannot be found', 'service-finder' ),

	'captcha_validate' => esc_html__( 'The Validation code does not match!', 'service-finder' ),

	'no_result' => esc_html__( 'No results matched', 'service-finder' ),

	'only_digits' => esc_html__( 'Please enter only digits', 'service-finder' ),

	'not_selected' => esc_html__( 'Nothing selected', 'service-finder' ),

	'empty_table' => esc_html__( 'No data available in table', 'service-finder' ),

	'captchaverify' => esc_html__( 'Please verify the captcha code', 'service-finder' ),

	'enablebusiness' => esc_html__( 'Enable Claim Business', 'service-finder' ),

	'disbalebusiness' => esc_html__( 'Disable Claim Business', 'service-finder' ),

	'applied' => esc_html__( 'Applied', 'service-finder' ),

	'group_req' => esc_html__( 'Please insert group name', 'service-finder' ),

	'video_req' => esc_html__( 'Please insert video url', 'service-finder' ),

	'google_client_id_req' => esc_html__( 'Please insert Google Client ID', 'service-finder' ),

	'google_client_secret_req' => esc_html__( 'Please insert Google Client Secret', 'service-finder' ),

	'perpersion' => esc_html__( 'Item', 'service-finder' ),

	'perhour' => esc_html__( 'Hour', 'service-finder' ),

	'perpersion_short' => esc_html__( 'Item', 'service-finder' ),

	'perhour_short' => esc_html__( 'Hr', 'service-finder' ),

	'radius_search' => esc_html__( 'Please fill address to search by radius', 'service-finder' ),

	'show_more' => esc_html__( 'Show More Location', 'service-finder' ),

	'show_less' => esc_html__( 'Show Less Location', 'service-finder' ),

	'currencysymbol' => service_finder_currencysymbol(),

	'only_numeric' => esc_html__( 'Please enter only numerics', 'service-finder' ),

	'yourname' => esc_html__( 'Your name', 'service-finder' ),

	'email' => esc_html__( 'Email', 'service-finder' ),

	'website' => esc_html__( 'Website', 'service-finder' ),

	'entercomments' => esc_html__( 'Enter your comments...', 'service-finder' ),

	'provider_approve_request' => esc_html__( 'Are you sure you want to approve this provider?', 'service-finder' ),

	'approve_request' => esc_html__( 'Are you sure you want to approve?', 'service-finder' ),

	'make_unfeatured' => esc_html__( 'Do you want to make this provider Unfeatured?', 'service-finder' ),

	'make_unbloacked' => esc_html__( 'Do you want to make this provider Unblocked?', 'service-finder' ),

	'currentvalue' => esc_html__( 'Current value', 'service-finder' ),

	'bookings_schedule' => esc_html__( 'Bookings Schedule', 'service-finder' ),

	'select_starttime' => esc_html__( 'Please select start time', 'service-finder' ),

	'select_endtime' => esc_html__( 'Please select end time', 'service-finder' ),

	'start_endtime_balance' => esc_html__( 'End time should be greater than start time', 'service-finder' ),

	'select_interval' => esc_html__( 'Please select slot interval', 'service-finder' ),

	'change_interval_warning' => esc_html__( 'After change current interval existing slots will be lost.', 'service-finder' ),

	'select_weekday' => esc_html__( 'Please select atleast one week day', 'service-finder' ),

	'payto_provider_confirm' => esc_html__( 'Are you sure you want to pay provider', 'service-finder' ),

	'payto_provider_change_status' => esc_html__( 'Are you sure you want to change payment status from pending to paid?', 'service-finder' ),

	'edit_member' => esc_html__( 'Edit Member', 'service-finder' ),

	'remain_on_same_page' => esc_html__( 'Remain on Same Page', 'service-finder' ),

	'continue_lable' => esc_html__( 'Continue', 'service-finder' ),

	'complete_booking_and_pay' => esc_html__( 'If you press ok then funds will be released by admin to provider.', 'service-finder' ),

	'file_message' => esc_html__( 'Please choose a file', 'service-finder' ),

	'csv' => esc_html__( 'File must be a CSV', 'service-finder' ),

	'import_success' => esc_html__( 'Providers Imported Successfully', 'service-finder' ),

	'import_categories_success' => esc_html__( 'Categories Imported Successfully', 'service-finder' ),

	'days' => esc_html__( 'Days', 'service-finder' ),

	'hours' => esc_html__( 'Hours', 'service-finder' ),

	'minutes' => esc_html__( 'Minutes', 'service-finder' ),

	'seconds' => esc_html__( 'Seconds', 'service-finder' ),

	'select_hours_text' => esc_html__( 'Please insert hours for service', 'service-finder' ),

	'unavl_days' => esc_html__( 'Select Unavailable Days', 'service-finder' ),

	'select_option' => esc_html__( 'Please select above option', 'service-finder' ),

	'valid_number' => esc_html__( 'Please insert a valid number', 'service-finder' ),

	'booking_dates' => esc_html__( 'Please select dates', 'service-finder' ),

	'select_region' => esc_html__( 'Please select region', 'service-finder' ),

	'insert_zipcode' => esc_html__( 'Please insert zipcode', 'service-finder' ),

	'insufficient_wallet_amount' => $walletwaring,

	'amount_range' => sprintf(esc_html__( 'Please insert amount between %d to %d', 'service-finder' ),$minwalletamount,$maxwalletamount),

	'payment_method_req' => esc_html__( 'Please select payment method', 'service-finder' ),

	'quote_reply' => esc_html__( 'Quotation Reply', 'service-finder' ),

	'edit_experience' => esc_html__( 'Edit Experience', 'service-finder' ),

	'set_member_timeslot' => esc_html__( 'Set Timeslots', 'service-finder' ),

	'wallet' => esc_html__( 'Wallet', 'service-finder' ),

	'wallet_balance' => esc_html__( 'Wallet Balance', 'service-finder' ),

	'checkout' => esc_html__( 'Checkout', 'service-finder' ),

	'skip_payment' => esc_html__( 'Skip Payment', 'service-finder' ),

	'have_coupon' => esc_html__( 'Have a coupon code', 'service-finder' ),

	'verify' => esc_html__( 'Verify', 'service-finder' ),

	'email_req' => esc_html__( 'Please fill email address to continue', 'service-finder' ),

	'edit_certificates' => esc_html__( 'Edit Certificate', 'service-finder' ),

	'edit_qualification' => esc_html__( 'Edit Qualification', 'service-finder' ),

	'email_exist' => esc_html__( 'Email already exist', 'service-finder' ),

	'username_exist' => esc_html__( 'Username already exist', 'service-finder' ),

	'edit_string' => esc_html__( 'Edit', 'service-finder' ),

	'price_req' => esc_html__( 'Please enter price', 'service-finder' ),

	'declinereason' => esc_html__( 'Short description about decline reason', 'service-finder' ),

	'sEmptyTable' => esc_html__( 'No data available in table', 'service-finder' ),

	'sInfo' => esc_html__( 'Showing _START_ to _END_ of _TOTAL_ entries', 'service-finder' ),

	'sInfoEmpty' => esc_html__( 'Showing 0 to 0 of 0 entries', 'service-finder' ),

	'sInfoFiltered' => esc_html__( '(filtered from _MAX_ total entries)', 'service-finder' ),

	'sLengthMenu' => esc_html__( 'Show _MENU_ entries', 'service-finder' ),

	'sLoadingRecords' => esc_html__( 'Loading...', 'service-finder' ),

	'sProcessing' => esc_html__( 'Processing...', 'service-finder' ),

	'sSearch' => esc_html__( 'Search:', 'service-finder' ),

	'sZeroRecords' => esc_html__( 'No matching records found', 'service-finder' ),

	'sFirst' => esc_html__( 'First', 'service-finder' ),

	'sLast' => esc_html__( 'Last', 'service-finder' ),

	'sNext' => esc_html__( 'Next', 'service-finder' ),

	'sPrevious' => esc_html__( 'Previous', 'service-finder' ),

	'sSortAscending' => esc_html__( ': activate to sort column ascending', 'service-finder' ),

	'sSortDescending' => esc_html__( ': activate to sort column descending', 'service-finder' ),

	'routing_number' => esc_html__( 'Routing Number', 'service-finder' ),

	'account_number' => esc_html__( 'Account Number', 'service-finder' ),

	'iban_number' => esc_html__( 'IBAN Number', 'service-finder' ),

	'sort_code' => esc_html__( 'Sort Code', 'service-finder' ),

	'invitation_sent' => esc_html__( 'Invitation Sent', 'service-finder' ),

	'bank_account_details' => esc_html__( 'Bank Account Details', 'service-finder' ),

	'bank_account_holder' => esc_html__( 'Bank Account Holder\'s Name', 'service-finder' ),

	'bank_account_number' => esc_html__( 'Bank Account Number/IBAN', 'service-finder' ),

	'bank_account_swiftcode' => esc_html__( 'Swift Code', 'service-finder' ),

	'bank_name' => esc_html__( 'Bank Name in Full', 'service-finder' ),

	'bank_branch_city' => esc_html__( 'Bank Branch City', 'service-finder' ),

	'bank_branch_country' => esc_html__( 'Bank Branch Country', 'service-finder' ),

	'bank_details_not_avl' => esc_html__( 'Provider didn\'t fill their Bank Details', 'service-finder' ),

	'add_wallet_balance' => esc_html__( 'Add Balance to Wallet', 'service-finder' ),

	'wallet_enter_amount' => esc_html__( 'Please Enter Amount', 'service-finder' ),

	'wallet_add_balance' => esc_html__( 'Add balance to wallet', 'service-finder' ),

	'add_to_wallet' => esc_html__( 'Add to Wallet', 'service-finder' ),

	'ifsc_code' => esc_html__( 'IFSC Code', 'service-finder' ),

	'select_hours_header' => esc_html__( 'Select hours', 'service-finder' ),

	'select_items_header' => esc_html__( 'Select items', 'service-finder' ),
	'readmore' => esc_html__( 'Read More', 'service-finder' ),
	'readless' => esc_html__( 'Read Less', 'service-finder' ),
	'send_reminder' => esc_html__( 'Send Reminder', 'service-finder' ),
	'stype_fixed' => esc_html__( 'Fixed', 'service-finder' ),
	'stype_hourly' => esc_html__( 'Hourly', 'service-finder' ),
	'stype_item' => esc_html__( 'Item', 'service-finder' ),
	'shours' => esc_html__( 'Hrs', 'service-finder' ),
	'sitems' => esc_html__( 'Items', 'service-finder' ),
	'enterzip' => esc_html__( 'Enter Zipcode', 'service-finder' ),
	'selectregion' => esc_html__( 'Select Zipcode', 'service-finder' ),
	'req_stripe_identity' => esc_html__( 'Please upload identity document', 'service-finder' )

);



wp_localize_script( 'bootstrap-select', 'param', $string_array );

wp_localize_script( 'service_finder-js-form-validation', 'param', $string_array );

wp_localize_script( 'service_finder-js-registration', 'param', $string_array );

wp_localize_script( 'service_finder-js-form-submit', 'param', $string_array );

wp_localize_script( 'service_finder-js-branches-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-availability-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-unavailability-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-bh-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-servicearea-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-service-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-job-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-team-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-bookings-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-invoice-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-invoice-customer-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-upgrade', 'param', $string_array );

wp_localize_script( 'service_finder-js-my-favorites', 'param', $string_array );

wp_localize_script( 'service_finder-js-quote-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-newsletter', 'param', $string_array );

wp_localize_script( 'service_finder-js-booking-form-v1', 'param', $string_array );

wp_localize_script( 'service_finder-js-booking-form-free-v1', 'param', $string_array );

wp_localize_script( 'service_finder-js-booking-form-v2', 'param', $string_array );

wp_localize_script( 'service_finder-js-booking-form-free-v2', 'param', $string_array );

wp_localize_script( 'service_finder-js-invoice-paid', 'param', $string_array );

wp_localize_script( 'service_finder-js-app', 'param', $string_array );

wp_localize_script( 'service_finder-js-schedule', 'param', $string_array );

wp_localize_script( 'service_finder-js-providers', 'param', $string_array );

wp_localize_script( 'admin-booking-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-admin-booking-form', 'param', $string_array );

wp_localize_script( 'service_finder-js-featured-requests', 'param', $string_array );

wp_localize_script( 'service_finder-js-invoice-requests', 'param', $string_array );

wp_localize_script( 'bootstrap-select', 'param', $string_array );

wp_localize_script( 'service_finder-js-quotations', 'param', $string_array );

wp_localize_script( 'service_finder-js-claimbusiness', 'param', $string_array );

wp_localize_script( 'service_finder-js-claimbusiness-payment', 'param', $string_array );

wp_localize_script( 'service_finder-js-job-apply', 'param', $string_array );

wp_localize_script( 'service_finder-js-custom', 'param', $string_array );

wp_localize_script( 'service_finder-js-custom-effects', 'param', $string_array );

wp_localize_script( 'service_finder-js-cities', 'param', $string_array );

wp_localize_script( 'service_finder-js-ratinglabels', 'param', $string_array );

wp_localize_script( 'service_finder-js-upgraderequest', 'param', $string_array );

wp_localize_script( 'service_finder-js-jobconnectrequest', 'param', $string_array );

wp_localize_script( 'bootstrap-calendar', 'param', $string_array );

wp_localize_script( 'service_finder-js-providerimport', 'param', $string_array );

wp_localize_script( 'service_finder-js-sitemap', 'param', $string_array );

wp_localize_script( 'countdown-min', 'param', $string_array );

wp_localize_script( 'service_finder-js-experience', 'param', $string_array );

wp_localize_script( 'service-finder-job-applications', 'param', $string_array );

wp_localize_script( 'service_finder-js-profile', 'param', $string_array );

wp_localize_script( 'service_finder-js-notifications', 'param', $string_array );

wp_localize_script( 'service_finder-js-payout', 'param', $string_array );

wp_localize_script( 'service_finder-js-wallet-request', 'param', $string_array );

wp_localize_script( 'service_finder-js-booking-form-v4', 'param', $string_array );

?>