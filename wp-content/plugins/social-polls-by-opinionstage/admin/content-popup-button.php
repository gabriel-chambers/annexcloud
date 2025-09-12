<?php
defined( 'ABSPATH' ) || die();
?>

<button data-opinionstage-content-launch class="button" type="button">
<img src="<?php echo esc_url( plugins_url( 'admin/images/os-icon.svg', plugin_dir_path( __FILE__ ) ) ); ?>" 
		style="position: relative; left: -3px; top: -2px; padding: 0; max-width: 19px;"
>
	<?php esc_html_e( 'Add a Poll, Survey, Quiz or Form', 'social-polls-by-opinionstage' ); ?>
</button>
