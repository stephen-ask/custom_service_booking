<?php 
$post_image = wp_get_attachment_image_url(5921, 'thumbnail');
?>
<form action="" id="create_new_post">
    <div class="panel panel-default about-me-here">
        <div class="panel-heading sf-panel-heading">
        <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Add New Post', 'service-finder'); ?> </h3>
        </div>
        <div class="panel-body sf-panel-body padding-30">
        <div class="row">
            <div class="col-lg-12">
            <div class="form-group">
                <label>
                    <?php esc_html_e('Post Title', 'service-finder'); ?>
                </label>
                <div class="input-group"> 
                <input type="text" class="form-control sf-form-control" id='title' name="title"  value="<?php //echo esc_attr($product->get_name()) ?>">
                </div>
            </div>
            </div>
            <div class="col-lg-12">
            <div class="form-group">
                <label>
                <?php esc_html_e('Post Content', 'service-finder'); ?>
                </label>
                <div class="form-group">
                    <textarea class="form-control" name='description' id="description" style="height:100px" ></textarea>  
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    <div class="panel panel-default gallery-images">
        <div class="panel-heading sf-panel-heading">
        <h3 class="panel-tittle m-a0"><span class="fa fa-image"></span> <?php esc_html_e('Featured Image', 'service-finder'); ?> </h3>
        <div class="panel-body sf-panel-body padding-30">
        <div class="row">
            <div class="col-md-12">
                <div class="sf-avtarinfo-wrapper sf-coverinfo-wrapper">
                    <div class="post sf-img-section">
                        <img src="<?=$post_image; ?>" alt="post Image" id='post_image_preview' srcset="">
                        <label for="post_image" class="custom-file-upload site-button">Upload Image</label>
                        <input type="file" name="post_image" id="post_image" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</form>
<script>
    (function($){
		jQuery('#post_image').change(function(event){
			const file = this.files[0];
			if (file){
				let reader = new FileReader();
				reader.onload = function(event){
					jQuery("#post_image_preview").attr('src', event.target.result);
				}
				reader.readAsDataURL(file);
			}
		});

        jQuery('#create_new_post').submit( async function(e){
            e.preventDefault();
            let token = localStorage.getItem('token') ?? '';
            let apiUrl = '<?=get_home_url(); ?>/wp-json/v1/post/add';

            let product_name = jQuery('#title').val();
            let description = jQuery('#description').val();
        
            let product_image = document.getElementById('post_image').files[0];
            var form = new FormData();

            form.append('title', product_name);
            form.append('description', slug);
            form.append('post_image', price);
         
			if(token != '') {
                const response = await fetch(apiUrl, {
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
    })(jQuery);
    
</script>
<?php wp_footer();?>

