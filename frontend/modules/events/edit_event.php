<?php
$event_image  = wp_get_attachment_image_url(5921, 'thumbnail');
// var_dump(@$_GET['meeting']);
?>
<style>
    .reoccuring {
        width: 100%;
    }
</style>
<form action="" id="edit_event">
<div class="panel panel-default about-me-here">
        <div class="panel-heading sf-panel-heading">
            <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Edit Event', 'service-finder'); ?> </h3>
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
                <!--
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
                
                 <div class="col-lg-12">
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
        var ajaxUrl = '<?=get_site_url(); ?>/wp-json/v1/meetings/get';
        var getdata = new FormData();
        
        let url = window.location.href;
        let event_id = url.split('?meeting=')[0];
        getdata.append('event_id', event_id);
        
        $('.event_action').click(function(){
            console.log(event_id);

            var fetch_data = async (ajaxUrl) => {
                var response = await fetch(ajaxUrl,  {
                    method: "POST",
                    body: getdata, 
                    headers: {
                        'Authorization': 'Bearer '+ token
                    }
                });
                var res = await response.json();
                console.log(response);
            }
            fetch_data(ajaxUrl);
        });

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

    jQuery('#edit_event').submit( async function(e){
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
        let reaccurring = 'off';
        let totaltickets = 99999;
        let price = regular_price = sale_price = stock = 0;
       
        let url = window.location.href;
        let event_id = url.split('?meeting=')[0];
        // getdata.append('event_id', event_id);

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
        form.append('_stock', stock);
        form.append('action', 'elementor_create_meeting');
        form.append('meeting_id', event_id);
        
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
            
            setTimeout(() => {
                jQuery('#create_new_event').prepend('<div class="alert '+alertClass+'">'+msg+'</div>');
            }, "2000");

        } else {
            console.log('Invalid Token');
        }
    });
</script>