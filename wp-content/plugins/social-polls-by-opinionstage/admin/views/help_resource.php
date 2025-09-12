<?php
/**
 * Opinionstage Get Help Admin page
 *
 * @package OpinionStageWordPressPlugin
 */

use Opinionstage\Infrastructure\Helper;
use Opinionstage\Infrastructure\HelperUTM;

defined( 'ABSPATH' ) || die();

$videos_data = array(
	array(
		'title'      => __( 'How to Use the Plugin', 'social-polls-by-opinionstage' ),
		'title_link' => 'https://help.opinionstage.com/en/articles/2557183-how-to-use-the-wordpress-plugin/',
		'video_id'   => 'DMcosYCBFDs',
	),
	array(
		'title'    => __( 'How to Create a Quiz', 'social-polls-by-opinionstage' ),
		'video_id' => 'PPNIezl_wu0',
	),
	array(
		'title'    => __( 'How to Create a Poll', 'social-polls-by-opinionstage' ),
		'video_id' => 'xFUAwszhiuo',
	),
	array(
		'title'    => __( 'How to Create a Survey', 'social-polls-by-opinionstage' ),
		'video_id' => 'sUcGbCGwn_Q',
	),
);


?>
<div id="opinionstage-content">
		<?php
		$user_email = ! empty( $os_options['email'] ) ? $os_options['email'] : '';
		require OPINIONSTAGE_PLUGIN_DIR . 'admin/template-parts/header-logo-line-logout-form.php';
		if ( $os_client_logged_in ) {
			?>
		<div class="opinionstage-tutorials-and-help">
			
			<div class="opinionstage-tutorials-and-help__hero">
				<div class="opinionstage-tutorials-and-help__container">
					<h1 class="opinionstage-tutorials-and-help__hero__header"><?php esc_html_e( 'Tutorials & Help', 'social-polls-by-opinionstage' ); ?></h1>
					
					<div class="opinionstage-tutorials-and-help__buttons">
						<a href="https://help.opinionstage.com" class=" opinionstage-button opinionstage-button__blue" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Go To Help Center', 'social-polls-by-opinionstage' ); ?></a>
                        <a href="<?php echo HelperUTM::get_templates_url_for_type('home'); ?>" class="opinionstage-button opinionstage-button__blue" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Templates & Examples', 'social-polls-by-opinionstage' ); ?></a>
						<a href="<?php echo esc_url( HelperUTM::utm_url( 'live-chat/' ) ); ?>" class="opinionstage-button opinionstage-button__blue" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Live Chat Support', 'social-polls-by-opinionstage' ); ?></a>
					</div>
				</div>
			</div>

			<div class="opinionstage-tutorials-and-help__videos">
				<div>
					<?php
					foreach ( $videos_data as $videos_datum ) {
						?>
						<div class="opinionstage-tutorials-and-help__video">
							<?php if ( isset( $videos_datum['title_link'] ) ) { ?>
							<a href="<?php echo esc_url( $videos_datum['title_link'] ); ?>" <?php echo Helper::get_link_target_blank_attribute(); ?> class="opinionstage-tutorials-and-help__video__title__link">
								<?php } ?>
								<h3 class="opinionstage-tutorials-and-help__video__title"><?php echo esc_html( $videos_datum['title'] ); ?></h3>
								<?php if ( isset( $videos_datum['title_link'] ) ) { ?>
							</a>
						<?php } ?>

							<div class="opinionstage-getting-started-video__iframe">
								<div>
									<iframe width="100%"
											src="https://www.youtube.com/embed/<?php echo esc_attr( $videos_datum['video_id'] ); ?>?controls=0&showinfo=0"
											title="YouTube video player" frameborder="0"
											allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
											allowfullscreen></iframe>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
