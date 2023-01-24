<?php
$event_image  = wp_get_attachment_image_url(5921, 'thumbnail');
?>
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
                            <input type="text" class="form-control sf-form-control" id='agenda' name="agenda"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Event Description', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="text" class="form-control sf-form-control" id='agenda' name="agenda"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Start Time', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="text" class="form-control sf-form-control" id='start_time' name="start_time"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('End Time', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="text" class="form-control sf-form-control" id='end_time' name="end_time"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('Start Date', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="text" class="form-control sf-form-control" id='start_date' name="start_date"  value="<?php //echo esc_attr($product->get_name()) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>
                            <?php esc_html_e('End Date', 'service-finder'); ?>
                        </label>
                        <div class="input-group"> 
                            <input type="text" class="form-control sf-form-control" id='end_date' name="end_date"  value="<?php //echo esc_attr($product->get_name()) ?>">
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
                                <option value='uMgIcYZSTwmjbBvd8AwtWQ'>test ironman</option>
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
                            <label for='reaccurring_on'><input type="radio" name='reaccurring' id='reaccurring_on' value='on' /> On</label>
                            <label for='reaccurring_off'><input type="radio" name='reaccurring' id='reaccurring_off' value='off' checked='checked' /> Off</label>
                        </div>
                    </div>
                </div>
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
    jQuery('#event_image').change(function(event){
        const file = this.files[0];
        if (file){
            let reader = new FileReader();
            reader.onload = function(event){
                jQuery("#event_image_preview").attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    jQuery('#create_new_event').submit( async function(e){
        e.preventDefault();
        let token = localStorage.getItem('token') ?? '';
        let ajaxUrl = '<?=get_home_url(); ?>wp-admin/admin-ajax.php';

        let product_name = jQuery('#title').val();
        let description = jQuery('#description').val();
    
        let product_image = document.getElementById('event_image').files[0];
        var form = new FormData();

        form.append('title', product_name);
        form.append('description', slug);
        form.append('event_image', price);
        
        form.append('title', product_name);
        form.append('description', slug);
        form.append('event_image', price);
        
        form.append('title', product_name);
        form.append('description', slug);
        form.append('event_image', price);
        
        if(token != '') {
            const response = await fetch(ajaxUrl, {
                method: 'POST',  
                body: form, 
                headers: {
                    'Authorization': 'Bearer '+ token
                }
            });
            const res = await response.json();
            
            var msg = (res == 'Success') ? 'Product Created' : 'Failed to Create Product';
            var alertClass = (res == 'Success') ? 'alert-success' : 'alert-danger';

            setTimeout(() => {
                jQuery('#create_new_product').prepend('<div class="alert '+alertClass+'">'+msg+'</div>');
            }, "2000");

        } else {
            console.log('Invalid Token');
        }
    });
</script>