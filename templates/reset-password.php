<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// If checks to determine which template/form to show the user
if ( isset( $_GET['sfresetpass'] ) && ( isset( $_GET['sfrp_action'] ) && $_GET['sfrp_action'] == 'rp' ) ) {

	$key = (isset($_GET['key'])) ? sanitize_text_field($_GET['key']) : '';
	$login = (isset($_GET['login'])) ? sanitize_text_field($_GET['login']) : '';
	
	$user = check_password_reset_key( $key, $login );

	if ( is_wp_error( $user ) ) {

		if ( $user->get_error_code() === 'expired_key' ) {

			echo '<div class="alert alert-danger">';
			echo esc_html__('That key has expired. Please reset your password again.', 'service-finder');
			echo '</div>';

		} else {

			$code = $user->get_error_code();
			if ( empty( $code ) ) {
				$code = '00';
			}
			$errors['invalid_key'] = __( 'That key is no longer valid. Please reset your password again. Code: ' . $code, 'frontend-reset-password' );
			echo '<div class="alert alert-danger">';
			echo esc_html__('That key is no longer valid. Please reset your password again. Code: ' . $code, 'service-finder');
			echo '</div>';

		}

	} else {

	?>
		<div class="row ">
          <form class="reset_new_password" method="post">
            <div class="col-md-12">
              <div class="form-group">
                <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                  <input name="new_pass" id="new_pass" type="password" class="form-control" placeholder="<?php esc_html_e('Password', 'service-finder'); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                  <input name="confirm_new_pass" id="confirm_new_pass" type="password" class="form-control" placeholder="<?php esc_html_e('Re-enter Password', 'service-finder'); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" name="action" value="resetnewpass" />
                <input type="hidden" name="key" value="<?php echo esc_attr($key); ?>" />
                <input type="hidden" name="login" value="<?php echo esc_attr($login); ?>" />
                <input type="submit" class="btn btn-primary btn-block" name="user-login" value="<?php esc_html_e('Reset New Password', 'service-finder'); ?>" />
              </div>
            </div>
          </form>
        </div>
	<?php	

	}

}

?>