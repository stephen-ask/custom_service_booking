<?php
$api_key = get_user_meta(get_current_user_id(), 'zoom_api_key', true);
$secret_key = get_user_meta(get_current_user_id(), 'zoom_secret_key',  true);
?>
<form id='zoom-settings'>
    <div class="panel panel-default about-me-here">
            <div class="panel-heading sf-panel-heading">
                <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Zoom Credentials', 'service-finder'); ?> </h3>
            </div>
            <div class="panel-body sf-panel-body padding-30">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>
                                <?php esc_html_e('Api key', 'service-finder'); ?>
                            </label>
                            <div class="form-group">
                                <input type="text" name="api_key" id="api_key" class='form-control' value="<?=$api_key;?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>
                                <?php esc_html_e('Secret key', 'service-finder'); ?>
                            </label>
                            <div class="form-group">
                                <input type="text" name="secret_key" id="secret_key" class='form-control' value="<?=$secret_key;?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="panel panel-default gallery-images">
        <div class="panel-heading sf-panel-heading">
            <input type="submit" value="Save Settings" class='btn-primary btn'>
        </div>
    </div>
</form>

<script>
 
    jQuery('#zoom-settings').submit( async function(e){
        e.preventDefault();
        let token = localStorage.getItem('token') ?? '<?=$_SESSION['token'];?>';
        let ajaxUrl = '<?=get_home_url(); ?>/wp-json/zoom/settings';

        let api_key = jQuery('#api_key').val();
        let secret_key = jQuery('#secret_key').val();
    
        
        var form = new FormData();

        form.append('api_key', api_key);
        form.append('secret_key', secret_key);
    
        
        if(token != '') {
            const response = await fetch(ajaxUrl, {
                method: 'POST',  
                body: form, 
                headers: {
                    'Authorization': 'Bearer '+ token
                }
            });
            const res = await response.json();
            
            var msg = (res.status == 'Success' || res.status == 'success') ? 'API keys updated' : 'Failed to update keys';
            var alertClass = (res.status == 'Success' || res.status == 'success') ? 'alert-success' : 'alert-danger';

            setTimeout(() => {
                jQuery('#zoom-settings').prepend('<div class="alert '+alertClass+'">'+msg+'</div>');
            }, "2000");

        } else {
            console.log('Invalid Token');
        }
    });
</script>