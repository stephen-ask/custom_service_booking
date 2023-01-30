<?php 

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

$user_id = get_current_user_id();
$product_id = sanitize_text_field(@$_GET['product_id']) ?? '';
$empty = true;

if($product_id) {
    $product = wc_get_product( $product_id );
}

$taxonomy     = 'product_cat';

$args = array(
       'taxonomy'     => $taxonomy,
       'hide_empty'   => $empty
);
$all_categories = get_categories( $args );
$options = '';
foreach ($all_categories as $cat) {
    $category_id = $cat->term_id;
    $args2 = array(
        'taxonomy'     => $taxonomy,
        'hide_empty'   => $empty,
        'parent'       => $category_id,
    );
    $options .= '<option value="'.$category_id.'">'.$cat->name.'</option>';
    $sub_cats = get_categories( $args2 );
    if($sub_cats) {
        foreach($sub_cats as $sub_category) {
            $category_id = $cat->term_id;
            $options .= '<option value="'.$category_id.'">'. $sub_category->name.'</option>';
        }   
    }
}
$product_image = wp_get_attachment_image_url(5921, 'thumbnail');
?>
<style>
	.js .tmce-active #description.wp-editor-area {
		color: #000;
	}
	.woo-product-gallery-container {
		border: 1px dotted #aaa;
		border-radius: 5px;
	}
</style>
<form method="POST" id="create_new_product">
<div class="panel panel-default about-me-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Add New Product', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
                <?php esc_html_e('Product Name', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <input type="text" class="form-control sf-form-control" id='product_name' name="product_name"  value="<?php //echo esc_attr($product->get_name()) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Product Slug', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <input type="text" class="form-control sf-form-control" id="slug" name="slug" value="<?php // echo esc_attr($userInfo['company_name']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Price', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <input type="text" class="form-control sf-form-control" name="price" id="price" value="<?php // echo esc_attr($userInfo['company_name']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Categories', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <select name="category_ids[]" id="category_ids"  class="form-control" multiple>
                <?=$options;?>
              </select>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('SKU', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <input type="text" class="form-control sf-form-control" id="sku" name="sku" value="<?php // echo esc_attr($userInfo['company_name']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Stock', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <input type="text" class="form-control sf-form-control" id="stocks" name="stocks" value="<?php // echo esc_attr($userInfo['company_name']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Product Description', 'service-finder'); ?>
            </label>
            <div class="form-group">
              <textarea class="form-control" name='description' id="description" style="height:100px" ></textarea>  
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!--Add cover image Section-->
  <div class="panel panel-default gallery-images">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-image"></span> <?php esc_html_e('Product Image', 'service-finder'); ?> </h3>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-md-12">
            <div class="sf-avtarinfo-wrapper sf-coverinfo-wrapper">
                <div class="product sf-img-section">
                    <img src="<?=$product_image; ?>" alt="Product Image" id='product_image_preview' srcset="">
                    <label for="product_image" class="custom-file-upload site-button">Upload Image</label>
                    <input type="file" name="product_image" id="product_image" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                </div>
            </div>
        </div>
        
      </div>
    </div>
  </div>  
</div>

  <div class="panel panel-default gallery-images">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-image"></span> <?php esc_html_e('Product Gallery', 'service-finder'); ?> </h3>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-md-12">
            <div class="sf-avtarinfo-wrapper sf-coverinfo-wrapper woo-product-gallery-container">
                <div class="product sf-img-section">
                    <label for="product_image" class="custom-file-upload site-button">Select Images</label>
                    <input type="file" name="product_gallery[]" id="product_gallery" multiple accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <input type="submit" value="Submit" class='btn btn-primary'>
        </div>
      </div>
    </div>
  </div>
  </div>

        </form>
<script>
    (function($){
		jQuery('#product_image').change(function(event){
			const file = this.files[0];
			if (file){
				let reader = new FileReader();
				reader.onload = function(event){
					jQuery("#product_image_preview").attr('src', event.target.result);
				}
				reader.readAsDataURL(file);
			}
		});
        jQuery('#product_name').focusout(function(){
            let value = jQuery('#product_name').val();
  
            let slug = value.toLowerCase();
            let slug_container = jQuery('#slug');
            slug = slug.replace(' ' ,'-');
            slug_container.val(slug);
        });
        jQuery('#create_new_product').submit( async function(e){
            e.preventDefault();
            let token = localStorage.getItem('token') ?? '';
            let apiUrl = '<?=get_home_url(); ?>/wp-json/v1/product/add';

            let product_name = jQuery('#product_name').val();
            let slug = jQuery('#slug').val();
            let price = jQuery('#price').val();
            let category_ids = jQuery('#category_ids').val();
            let description = jQuery('#description').val();
            let sku = jQuery('#sku').val();
            let stocks = jQuery('#stocks').val();
         
            let product_gallery = document.getElementById('product_gallery').files[0];
            let product_image = document.getElementById('product_image').files[0];
            var form = new FormData();

            form.append('product_name', product_name);
            form.append('slug', slug);
            form.append('price', price);
            form.append('category_ids', category_ids);
            form.append('description', description);
            form.append('product_image', product_image);
            form.append('product_gallery', product_gallery);
            form.append('sku', sku);
            form.append('stocks', stocks);
					
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
<?php //wp_footer();?>

