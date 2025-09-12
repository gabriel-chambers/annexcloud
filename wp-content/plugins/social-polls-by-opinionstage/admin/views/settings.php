<?php
/**
 * Opinionstage Create Admin page
 *
 * @package OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();
?>

<style type="text/css">
	.content-item-image.quiz{
		background-image: url(<?php echo esc_url( Opinionstage::get_instance()->plugin_url . '/admin/images/form-not-found.png' ); ?>);
	}
</style>
<div id="opinionstage-content">
	<div opinionstage-my-items-view class="opinionstage-my-items-view"></div>
	<div id="opinionistage-my-items-page-modal-wrapper">
		<div class="opinionistage-my-items-page-modal">
			<div class="inner">
				<h3 class="opinionstage-modal-title"><?php esc_html_e( 'Add to site', 'social-polls-by-opinionstage' ); ?></h3>

				<p><b><?php esc_html_e( 'If you are using the Gutenberg editor:', 'social-polls-by-opinionstage' ); ?></b></p>
				<ol>
					<li><?php esc_html_e( 'Edit the post/page where you’d like to embed the item.', 'social-polls-by-opinionstage' ); ?></li>
					<li><?php esc_html_e( 'Add the Opinion Stage block', 'social-polls-by-opinionstage' ); ?></li>
					<li><?php esc_html_e( 'Follow the instructions on the block to embed the item.', 'social-polls-by-opinionstage' ); ?></li>
				</ol>

				<p><b><?php esc_html_e( 'If you are using the Classic editor:', 'social-polls-by-opinionstage' ); ?></b></p>

				<div class="opinionstage-copy-shortcode">
					<input class="opinionstage-copy-shortcode__textarea" name="opinionstage-widget-shortcode" id="opinionstage-widget-shortcode" data-wp-embed-code rows="2" readonly="readonly" />
					<a data-copy-text-from="data-wp-embed-code" href="#" class="opinionstage-button opinionstage-button__black"><?php esc_html_e( 'Copy', 'social-polls-by-opinionstage' ); ?></a>
				</div>

				<ol>
					<li>Edit the post/page.</li>
					<li>Copy the short code above</li>
					<li>Paste it where you’d like to embed the item.</li>
				</ol>
				<div class="opinionistage-my-items-page-modal__close-button">
					<span id="opinionstage-dialog-close" class="opinionstage-button">Close</span>
				</div>
			</div>
		</div>
	</div>
</div>
