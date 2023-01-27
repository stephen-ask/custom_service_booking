<?php
$event_image  = wp_get_attachment_image_url(5921, 'thumbnail');
?>
<style>
    .reoccuring {
        width: 100%;
    }
</style>
<form action="" id="create_new_event">
<div class="panel panel-default about-me-here">
        <div class="panel-heading sf-panel-heading">
            <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Add New Event', 'service-finder'); ?> </h3>
        </div>
        <div class="panel-body sf-panel-body padding-30">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Event Title', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="text" class="form-control sf-form-control" id='topic' name="topic"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
         
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Event Description', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <textarea  class="form-control sf-form-control" id='agenda' name="agenda"  value=""></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Start Time', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="time" class="form-control sf-form-control" id='start_time' name="start_time"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('End Time', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="time" class="form-control sf-form-control" id='end_time' name="end_time"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Start Date', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="date" class="form-control sf-form-control" id='start_date' name="start_date"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('End Date', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="date" class="form-control sf-form-control" id='end_date' name="end_date"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Meeting Host', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                        <select name="user_id" id="user_id">
                                <option value='0'>Select User</option>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Recurring', 'service-finder'); ?>
                        </label>
                        <div class="form-group">
                            <label for='reaccurring_on'><input type="radio" name='reaccurring' class='reoccuring' id='reaccurring_on' value='on' /> On</label>
                            <label for='reaccurring_off'><input type="radio" name='reaccurring'class='reoccuring' id='reaccurring_off' value='off' checked='checked' /> Off</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Total Tickets', 'service-finder'); ?>
                        </label>
                        <div class="form-group">
                           <input type="text" name="tickets" id="tickets" class="form-control" />
                        </div>
                    </div>
                </div>
                <!-- Price -->
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Price', 'service-finder'); ?>
                        </label>
                        <div class="form-group">
                           <input type="text" name="price" id="price" class="form-control" />
                        </div>
                    </div>
                </div>
                <!-- Stock -->
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Regular price', 'service-finder'); ?>
                        </label>
                        <div class="form-group">
                           <input type="text" name="regular_price" id="regular_price" class="form-control" />
                        </div>
                    </div>
                </div>
                <!-- Stock -->
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Sale Price', 'service-finder'); ?>
                        </label>
                        <div class="form-group">
                           <input type="text" name="sale_price" id="sale_price" class="form-control" />
                        </div>
                    </div>
                </div>
                <!-- Stock -->
                <!-- <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Stock', 'service-finder'); ?>
                        </label>
                        <div class="form-group">
                           <input type="text" name="stock" id="stock" class="form-control" />
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    <!-- <div class="panel panel-default gallery-images">
        <div class="panel-heading sf-panel-heading">
        <h3 class="panel-tittle m-a0"><span class="fa fa-image"></span> <?php esc_html_e('Event Banner Image', 'service-finder'); ?> </h3>
        <div class="panel-body sf-panel-body padding-30">
        <div class="row">
            <div class="col-md-12">
                <div class="sf-avtarinfo-wrapper sf-coverinfo-wrapper">
                    <div class="event sf-img-section">
                        <img src="<?=$event_image; ?>" alt="Event Image" id='event_image_preview' srcset="">
                        <label for="event_image" class="custom-file-upload site-button">Upload Image</label>
                        <input type="file" name="event_image" id="event_image" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div> -->
    <div class="panel panel-default gallery-images">
        <div class="panel-heading sf-panel-heading">
            <input type="submit" value="Create a Meeting" class='btn-primary btn'>
        </div>
    </div>
</form>
<script>

    (function($){

        var today = new Date();
        var token = localStorage.getItem('token') ?? '<?= $_SESSION['token'];?>';
        var ajaxUrl = '<?=get_site_url(); ?>/wp-json/zoom/user/list';

        var fetch_users = async (ajaxUrl) => {

                var response = await fetch(ajaxUrl,  {
                    headers: {
                        'Authorization': 'Bearer '+ token
                    }
                });
                var res = await response.json();
                var option = bs_option ='';

                Object.entries(res.users).forEach((array) => {
                    array.forEach((value, index) => {
                
                        if( value.id == undefined) return; 

                        option += '<option value="'+ value.id +'">'+ value.display_name + '</option>'; 

                        bs_option += '<li  data-original-index="'+index+'">';
                            bs_option += '<a data-tokens="null" tabindex="'+index+'"><span class="text">'+value.display_name+'</span></a>';
                        bs_option += '</li>';

                    });
                });
            jQuery('#user_id').append(option);
            console.log(option, bs_option);
            jQuery("select + .bootstrap-select .dropdown-menu.inner").append(bs_option);
        } 



       fetch_users(ajaxUrl); // .then(res => console.log('res '+ res));
       document.getElementById("start_time").defaultValue = today.getHours() + ":" + today.getMinutes(); 

       $('#event_image').change(function(event){
        // const file = this.files[0];
        // if (file){
        //     let reader = new FileReader();
        //     reader.onload = function(event){
        //         $("#event_image_preview").attr('src', event.target.result);
        //     }
        //     reader.readAsDataURL(file);
        // }
    });
   })(jQuery);

    jQuery('#create_new_event').submit( async function(e){
        e.preventDefault();
        let token = localStorage.getItem('token') ?? '<?=$_SESSION['token'];?>';
        let ajaxUrl = '<?=get_home_url(); ?>/wp-admin/admin-ajax.php';

        let title = jQuery('#topic').val();
        let description = jQuery('#agenda').val();
        let user_id = jQuery('#user_id').val();
        let zoom_meeting_host = jQuery('#user_id').val();
        let etn_start_date = jQuery('#start_date').val();
        let etn_end_date = jQuery('#end_date').val();

        let etn_start_time = jQuery('#start_time').val();
        let etn_end_time = jQuery('#end_time').val();
        let reaccurring = jQuery('input[name="reaccurring"]:checked').val();
        let totaltickets = jQuery('#tickets').val();
        let price = jQuery('#price').val() ?? 0;
        let regular_price = jQuery('#regular_price').val() ?? 0;
        let sale_price = jQuery('#sale_price').val() ?? 0;
        let stock = jQuery('#stock').val() ?? 0;
        
        // let product_image = document.getElementById('event_image').files[0];
        var form = new FormData();

        form.append('topic', title);
        form.append('agenda', description);
        
        
        form.append('start_time', etn_start_date + ' ' + etn_start_time + ':00');

        form.append('etn_start_date', etn_start_date);
        form.append('etn_end_date', etn_end_date);
        form.append('etn_start_time', etn_start_time);
        form.append('etn_end_time', etn_end_time);
        
        form.append('recurring_enabled', reaccurring);
        
        form.append('current_user_id', <?=get_current_user_id(); ?>);
        // zoom 
        form.append('etn_zoom_event', 'on');
        form.append('type', 2);
        form.append('zoom_meeting_host', zoom_meeting_host);
        form.append('user_id', user_id);

        // ticket
        form.append('etn_ticket_availability', 'on');
        form.append('etn_total_avaiilable_tickets', totaltickets);
        form.append('_price', price);
        form.append('_regular_price', regular_price);
        form.append('_sale_price', sale_price);
        form.append('_stock', stock);
        form.append('action', 'elementor_create_meeting');
        
        
        if(token != '') {
            const response = await fetch(ajaxUrl, {
                method: 'POST',  
                body: form, 
                headers: {
                    'Authorization': 'Bearer '+ token,
                    'Access-Control-Allow-Origin': '*',
                    'Accept':'application/json; charset=UTF-8'
                }
            });
            const res = await response.json();
            
            var msg = (res.success == true ) ? 'Meeting Created' : 'Failed to create a Meeting';
            var alertClass = (res.success == true ) ? 'alert-success' : 'alert-danger';
            
            console.log(res.success);

            setTimeout(() => {
                jQuery('#create_new_event').prepend('<div class="alert '+alertClass+'">'+msg+'</div>');
            }, "2000");

        } else {
            console.log('Invalid Token');
        }
    });
</script>