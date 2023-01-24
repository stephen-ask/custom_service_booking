<?php 

/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<!--Template for dispaly featured requests-->





<div class="sf-wpbody-inr">

  <div class="sedate-title">

    <h2>

      <?php esc_html_e( 'Provider Import', 'service-finder' ); ?>

    </h2>

  </div>

  <div class="table-responsive">

   <div class="wrap">	

   		<div id="success"></div>

		<div class="provider-import-wrap">

		<form id="upload_csv" method="post" enctype="multipart/form-data">

		<input type="hidden" name="records" id="no_records" value="0">

		<input type="hidden" name="csv_num_rows" id="csv_num_rows" value="0">

			<table class="form-table">

				<tbody>

				<tr class="form-field form-required">

					<th scope="row"><label><?php esc_html_e( 'Update existing users?', 'service-finder' ); ?></label></th>

					<td>

                    	<div class="sf-select-wrap">	

						<select class="sf-select-box form-control sf-form-control" name="update_existing_users" id="update_existing_users">

							<option value="yes"><?php esc_html_e( 'Yes', 'service-finder' ); ?></option>

							<option value="no"><?php esc_html_e( 'No', 'service-finder' ); ?></option>

						</select>

                        </div>

					</td>

				</tr>



				<tr class="form-field form-required">

					<th scope="row"><label><?php esc_html_e( 'CSV file', 'service-finder');?> <span class="description">(<?php esc_html_e( 'required', 'service-finder');?>)</span></label></th>

					<td>

						<div id="upload_file" class="upload-file-input">
							<input type="file" name="uploadfiles" id="uploadfiles" size="35" class="uploadfiles" />
						</div>
                        <div class="upload-file-btn">	<i class="fa fa-cloud-upload"></i><?php esc_html_e( 'Upload Your File', 'service-finder');?></div>

					

					</td>

					</th>

				</tr>

                

                <tr class="form-field form-required">

					<th><input class="btn btn-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php esc_html_e( 'Start Importing', 'service-finder' ); ?>"/></th>

					<td>

                    	<?php $downloadurl = SERVICE_FINDER_BOOKING_LIB_URL.'/downloads.php?file='.SERVICE_FINDER_BOOKING_INC_URL.'/sample-provider-import.csv'; ?>

                        <a href="<?php echo esc_url($downloadurl); ?>" class="btn btn-us btn-download"><i class="fa fa-download"></i> <?php esc_html_e( 'Download Sample CSV FILE', 'service-finder' ); ?></a>

					</td>

					</th>

				</tr>



			</tbody>

			</table>

			<span class="sf-importnote"><label><?php esc_html_e( 'Note:', 'service-finder' ); ?></label> <?php esc_html_e( 'Your csv must have same number of columns in same order as given in our sample csv.', 'service-finder' ); ?></span>
			
			

			

			</form>
            
            <div class="provider-form-overlay" style="display:none"></div>

            <div class="provider-loading-image" style="display:none"><img src="<?php echo esc_url(SERVICE_FINDER_BOOKING_IMAGE_URL.'/load.gif'); ?>"></div>

		</div>
        
        	<div class="sedate-title">

            <h3>
        
              <?php esc_html_e( 'Category Import', 'service-finder' ); ?>
        
            </h3>
        
          </div>
        
        <div class="provider-import-wrap">

			<form id="categoryimport" method="post" enctype="multipart/form-data">
    			<input type="hidden" name="action" value="import_categories">
                <table class="form-table">
    
                    <tbody>
    
                    <tr class="form-field form-required">
    
                        <th scope="row"><label><?php esc_html_e( 'Update existing category?', 'service-finder' ); ?></label></th>
    
                        <td>
    
                            <div class="sf-select-wrap">	
    
                            <select class="sf-select-box form-control sf-form-control" name="update_existing_category" id="update_existing_category">
    
                                <option value="yes"><?php esc_html_e( 'Yes', 'service-finder' ); ?></option>
    
                                <option value="no"><?php esc_html_e( 'No', 'service-finder' ); ?></option>
    
                            </select>
    
                            </div>
    
                        </td>
    
                    </tr>
    
    
    
                    <tr class="form-field form-required">
    
                        <th scope="row"><label><?php esc_html_e( 'CSV file', 'service-finder');?> <span class="description">(<?php esc_html_e( 'required', 'service-finder');?>)</span></label></th>
    
                        <td>
    
                            <div id="upload_catfile" class="upload-file-input">
                                <input type="file" name="categorycsv" id="categorycsv" size="35" class="categorycsv" />
                            </div>
                            <div class="upload-file-btn">	<i class="fa fa-cloud-upload"></i><?php esc_html_e( 'Upload Your File', 'service-finder');?></div>
    
                        
    
                        </td>
    
                        </th>
    
                    </tr>
    
                    
    
                    <tr class="form-field form-required">
    
                        <th><input class="btn btn-primary" type="submit" name="uploadcatcsv" id="uploadcatcsv_btn" value="<?php esc_html_e( 'Start Importing', 'service-finder' ); ?>"/></th>
    
                        <td>
    
                            <?php $downloadurl = SERVICE_FINDER_BOOKING_LIB_URL.'/downloads.php?file='.SERVICE_FINDER_BOOKING_INC_URL.'/sample-categories-import.csv'; ?>
    
                            <a href="<?php echo esc_url($downloadurl); ?>" class="btn btn-us btn-download"><i class="fa fa-download"></i> <?php esc_html_e( 'Download Sample CSV FILE', 'service-finder' ); ?></a>
    
                        </td>
    
                        </th>
    
                    </tr>
    
    
    
                </tbody>
    
                </table>
    
                <span class="sf-importnote"><label><?php esc_html_e( 'Note:', 'service-finder' ); ?></label> <?php esc_html_e( 'Your csv must have same number of columns in same order as given in our sample csv.', 'service-finder' ); ?></span>
                
                </form>
                
            <div class="category-form-overlay" style="display:none"></div>

            <div class="category-loading-image" style="display:none"><img src="<?php echo esc_url(SERVICE_FINDER_BOOKING_IMAGE_URL.'/load.gif'); ?>"></div>    

            
		</div>



	</div>

  </div>

</div>