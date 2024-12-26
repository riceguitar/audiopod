<?php
/**
 * Admin View: Settings
 *
 * @package SM/Core/Admin/Settings
 */

defined( 'ABSPATH' ) or die;

$current_tab = empty( $current_tab ) ? 'general' : $current_tab;
?>
<div class="wrap sm sm_settings_<?php echo $current_tab; ?>">
	<div class="intro">
		<h1 class="wp-heading-inline">AudioPod Settings</h1>
	</div>
	<?php SM_Admin_Settings::show_messages(); ?>
	<div class="settings-main">
		<div class="settings-content">
			<form method="<?php echo esc_attr( apply_filters( 'sm_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>"
					id="mainform" action="" enctype="multipart/form-data">
				<nav class="nav-tab-wrapper sm-nav-tab-wrapper">
					<?php
					foreach ( $tabs as $name => $label ) {
						echo '<a href="' . admin_url( 'edit.php?post_type=wpfc_sermon&page=sm-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
					}
					do_action( 'sm_settings_tabs' );
					?>
				</nav>
				<div class="inside">
					<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
					<?php
					do_action( 'sm_sections_' . $current_tab );
					do_action( 'sm_settings_' . $current_tab );
					?>
					<p class="submit">
						<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
							<input name="save" class="button-primary sm-save-button" type="submit"
									value="<?php esc_attr_e( 'Save changes', 'audiopod-wp' ); ?>"/>
						<?php endif; ?>
						<?php wp_nonce_field( 'sm-settings' ); ?>
					</p>
				</div>
			</form>
		</div>
		<div class="settings-side">
			<div class="postbox sm-box" style="background: #f6fbff; display: block;
    padding: 0 .7rem;">
				<h2><span>IMPORTANT: Only if sermon content in missing</span>
				</h2>
				
				<div class="inside">
					<p>After updating to the latest version, please click the "Sync Now" button to resolve the issue.</p>
					<div style="text-align:center">
						<?php wp_nonce_field( 'sync_sermon_content_action', 'sync_sermon_content_nonce' ); ?>

						<a href="#" id="sync_sermon_old_data"class="button-primary">Sync Now</a>
					</div>
					
				</div>
			</div>
		   <?php  
			echo apply_filters( 'settings_page_sidebar_extra_boxs', $arg='' );
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function ($) {
		// console.log("function clicked11");
	$('#sync_sermon_old_data').click(function(e) {
        e.preventDefault(); // Prevent the default behavior of the button
        var nonce=jQuery('#sync_sermon_content_nonce').val();
        console.log("nonce === ",nonce);
        if (confirm('Are you sure you want to sync the data?')) {
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'sync_sermon_data',
                    sync_sermon_content_nonce: nonce
                },
                success: function(response) {                	
                	alert(response.data.message);
                	// console.log("response === ",response);
                    // 
                },
                error: function(e) {
                    alert('Something went wrong, please try again.',e);
                }
            });
        }
    });
});
</script>
