<?php
/**
 * Opinionstage Getting Started Admin page
 *
 * @package OpinionStageWordPressPlugin */

use Opinionstage\Infrastructure\Helper;

defined( 'ABSPATH' ) || die();

$links_columns_items = array(
	array(
		'title' => __( 'Quiz Templates', 'social-polls-by-opinionstage' ),
		'items' => array(
			array(
				'path'  => 't/personality-quiz-template',
				'title' => __( 'Personality Quiz', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/trivia-quiz-template',
				'title' => __( 'Trivia Quiz', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/lead-quiz-template',
				'title' => __( 'Lead Quiz', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/quiz-competition-template',
				'title' => __( 'Competition Quiz', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 'c/quizzes/types',
				'title' => __( 'All Quiz Types', 'social-polls-by-opinionstage' ),
			),
		),
	),
	array(
		'title' => __( 'Poll Templates', 'social-polls-by-opinionstage' ),
		'items' => array(
			array(
				'path'  => 't/list-poll-single-answer',
				'title' => __( 'Standard poll', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/image-poll',
				'title' => __( 'Image poll', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/thumbnail-poll',
				'title' => __( 'Thumbnail poll', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/head-to-head-poll',
				'title' => __( 'Head to Head poll', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/rounded-image-poll',
				'title' => __( 'Rounded-Image Poll', 'social-polls-by-opinionstage' ),
			),
		),
	),
	array(
		'title' => __( 'Survey Templates', 'social-polls-by-opinionstage' ),
		'items' => array(
			array(
				'path'  => 't/customer-feedback-survey',
				'title' => __( 'Feedback survey', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/how-do-you-feel-about-working-from-home-',
				'title' => __( 'Satisfaction survey', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/user-experience-questionnaire',
				'title' => __( 'User experience survey', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 't/website-design-questionnaire',
				'title' => __( 'Website design survey ', 'social-polls-by-opinionstage' ),
			),
			array(
				'path'  => 'c/surveys',
				'title' => __( 'All Survey Templates', 'social-polls-by-opinionstage' ),
			),
		),
	),
);

$plugin_logos_path_url = plugin_dir_url( OPINIONSTAGE_PLUGIN_FILE ) . 'admin/images/logos/';
$client_logos          = array(
	array(
		'url'   => $plugin_logos_path_url . 'nbc.png',
		'alt'   => __( 'NBC', 'social-polls-by-opinionstage' ),
		'width' => 73.5,
	),
	array(
		'url'   => $plugin_logos_path_url . 'wb.png',
		'alt'   => __( 'WP', 'social-polls-by-opinionstage' ),
		'width' => 28.5,
	),
	array(
		'url'   => $plugin_logos_path_url . 'uber.png',
		'alt'   => __( 'Uber', 'social-polls-by-opinionstage' ),
		'width' => 54.5,
	),
	array(
		'url'   => $plugin_logos_path_url . 'ipg.png',
		'alt'   => __( 'IPG', 'social-polls-by-opinionstage' ),
		'width' => 38.5,
	),
	array(
		'url'   => $plugin_logos_path_url . 'bbdo.png',
		'alt'   => __( 'BBDO', 'social-polls-by-opinionstage' ),
		'width' => 57,
	),
	array(
		'url'   => $plugin_logos_path_url . 'harvard.png',
		'alt'   => __( 'Harvard Law School Wordmark', 'social-polls-by-opinionstage' ),
		'width' => 103,
	),
	array(
		'url'   => $plugin_logos_path_url . 'virgin.png',
		'alt'   => __( 'Virgin Group', 'social-polls-by-opinionstage' ),
		'width' => 49,
	),
	array(
		'url'   => $plugin_logos_path_url . 'pepsico.png',
		'alt'   => __( 'Pepsico', 'social-polls-by-opinionstage' ),
		'width' => 129,
	),
);
?>
<div id="opinionstage-content">
	<div class="opinionstage-bg-white">
		<?php require OPINIONSTAGE_PLUGIN_DIR . 'admin/template-parts/header-logo-line-logout-form.php'; ?>
		<div class="opinionstage-getting-started-section opinionstage-two-columns">
			<div class="opinionstage-two-columns__text">
				<h1 class="opinionstage-two-columns__title"><?php esc_html_e( 'Add Quizzes, Polls & Surveys to Your Site', 'social-polls-by-opinionstage' ); ?></h1>
				<div>
					<p><?php esc_html_e( 'Get more responses with visual conversational quizzes, surveys & polls. Easy and fast to set up. Engaging and fun for your audience. Customize everything.', 'social-polls-by-opinionstage' ); ?></p>
				</div>

				<?php require_once plugin_dir_path( __DIR__ ) . 'template-parts/signup-form.php'; ?>
				<div>
				</div>
			</div>
			<div class="opinionstage-two-columns__img">
				<img src="<?php echo esc_url( plugins_url( 'images/welcome-to-opinionstage.png', __DIR__ ) ); ?>" alt="<?php esc_html_e( 'Welcome to Opinion Stage', 'social-polls-by-opinionstage' ); ?>">
			</div>
		</div>

		<div class="opinionstage-getting-started-section opinionstage-getting-started-section__trusted">
			<h2 class="opinionstage-trusted"><?php esc_html_e( 'Trusted by', 'social-polls-by-opinionstage' ); ?> 100,000+ <?php esc_html_e( 'Customers across 190 countries', 'social-polls-by-opinionstage' ); ?></h2>
			<ul class="opinionstage-clients">
				<?php
				foreach ( $client_logos as $client_logo ) {
					?>
					<li><img src="<?php echo esc_url( $client_logo['url'] ); ?>" alt="<?php echo esc_attr( $client_logo['url'] ); ?>" width="<?php echo esc_attr( $client_logo['width'] ); ?>"></li>
					<?php
				}
				?>
			</ul>
		</div>
	</div>

	<div class="opinionstage-bg-white">
		<div class="opinionstage-getting-started-section">
			<div class="opinionstage-getting-started-examples">
				<h2 class="opinionstage-getting-started-examples__title"><?php esc_html_e( 'Check Out These Examples:', 'social-polls-by-opinionstage' ); ?></h2>
				<div class="opinionstage-getting-started-examples__items">
					<?php
					foreach ( $links_columns_items as $col ) {
						?>
						<div class="opinionstage-getting-started-examples__item">
							<h3 class="opinionstage-getting-started-examples__item__title"><?php echo esc_html( $col['title'] ); ?></h3>
							<ul class="opinionstage-getting-started-examples__list">
								<?php
								foreach ( $col['items'] as $anchor ) {
									?>
									<li>
										<a href="<?php echo esc_url( Helper::generate_template_url( $anchor['path'] ) ); ?>" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php echo esc_html( $anchor['title'] ); ?></a>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="opinionstage-getting-started-section opinionstage-getting-started-video">
			<h2 class="opinionstage-getting-started-examples__title"><?php esc_html_e( 'How to Use the Plugin', 'social-polls-by-opinionstage' ); ?></h2>

			<p class="opinionstage-getting-started-video__description"><?php esc_html_e( 'Follow these steps to create a poll, survey or quiz and add it to your site in minutes', 'social-polls-by-opinionstage' ); ?></p>

			<div class="opinionstage-getting-started-video__iframe">
				<div>
					<iframe width="100%"
						src="https://www.youtube.com/embed/DMcosYCBFDs?controls=0&showinfo=0"
						title="YouTube video player" frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
