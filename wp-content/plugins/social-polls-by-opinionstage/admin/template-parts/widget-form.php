<?php
/**
 * Opinionstage Widget Form
 *
 * @package OpinionStageWordPressPlugin
 */

use Opinionstage\Infrastructure\Helper;

?>
<div class="opinionstage-sidebar-widget">
	<?php if ( $os_client_logged_in ) { ?>

		<img src="<?php echo esc_url( $logo_url ); ?>">
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'social-polls-by-opinionstage' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" placeholder="<?php esc_html_e( 'Enter the title here', 'social-polls-by-opinionstage' ); ?>" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		if ( $header_above_selected_widget ) {
			?>
			<label><?php echo esc_html( $header_above_selected_widget ); ?></label>
			<?php
		}
		$style = 'display:none;';
		if ( $os_widget_title || $os_widget_img_url ) {
			$style = '';
		}
		?>
		<div data-os-widget-id="<?php echo esc_attr( $this->id ); ?>">
			<div class="opinionstage-selected-widget" style="<?php echo esc_attr( $style ); ?>">
				<div class="opinionstage-widget-img-url-wrapper">
					<div class="inner">
						<?php if ( $os_widget_img_url ) { ?>
							<img src="<?php echo esc_url( $os_widget_img_url ); ?>">
						<?php } ?>
					</div>

					<div class="opinionstage-widget-overlay">
						<div class="opinionstage-inner">
							<a href="<?php echo esc_url( $os_widget_view_url ); ?>" class="opininstage-view" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'View', 'social-polls-by-opinionstage' ); ?></a>
							<a href="<?php echo esc_url( $os_widget_edit_url ); ?>" class="opininstage-edit" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Edit', 'social-polls-by-opinionstage' ); ?></a>
							<a href="<?php echo esc_url( $os_widget_stats_url ); ?>" class="opininstage-stats" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Statistics', 'social-polls-by-opinionstage' ); ?></a>
						</div>
					</div>
				</div>
				<span class="opinionstage-widget-title"><?php echo esc_html( $os_widget_title ); ?></span>
			</div>

			<div class="opinionstage-sidebar-actions">
				<div class="opinionstage-sidebar-config">
					<a data-open-popup-for-widget="<?php echo esc_attr( $this->id ); ?>" href="#" <?php echo Helper::get_link_target_blank_attribute(); ?> class='opinionstage-blue-bordered-btn opinionstage-edit-content'><?php echo esc_html( $popup_button_title ); ?></a>
				</div>
				<div class="opinionstage-clearfix"></div>
				<textarea name="<?php echo esc_attr( $this->get_field_name( 'opinionstage-widget-data' ) ); ?>" class="opinionstage-widget-data"><?php echo esc_attr( $os_widget_json ); ?></textarea>

				<div class="opinionstage-sidebar-enabled">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'enabled' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'enabled' ) ); ?>" value="1" <?php checked( $enabled, 1, true ); ?> />
					<label for="<?php echo esc_attr( $this->get_field_id( 'enabled' ) ); ?>"><?php esc_html_e( 'Enable widget', 'social-polls-by-opinionstage' ); ?></label>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<img src="<?php echo esc_url( $logo_url ); ?>" alt="opinionstage">
		<p class="connection_message"><?php esc_html_e( 'Connect WordPress with Opinion Stage to enable the widget' ); ?></p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=opinionstage-getting-started' ) ); ?>" class="opinionstage-button opinionstage-button__pink opinionstage-button__uppercase"><?php esc_html_e( 'Connect', 'social-polls-by-opinionstage' ); ?></a>
	<?php } ?>
</div>
