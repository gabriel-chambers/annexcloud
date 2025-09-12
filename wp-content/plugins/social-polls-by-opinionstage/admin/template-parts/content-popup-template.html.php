<?php
/**
 * Content Popup Template
 *
 * @package OpinionStageWordPressPlugin
 */

use Opinionstage\Infrastructure\Helper;
use Opinionstage\Infrastructure\TemplatesViewer;

defined( 'ABSPATH' ) || die();

$opinionstage_user_logged_in = Helper::is_user_logged_in();
$os_options                  = (array) get_option( OPINIONSTAGE_OPTIONS_KEY );
$is_my_items_admin_page      = Helper::is_my_items_admin_page();
$user_email                  = '';
if ( $is_my_items_admin_page ) {
	$user_email = ! empty( $os_options['email'] ) ? $os_options['email'] : '';
}
// Note: all html put here (not moved to js build system) in order to preserve ability to use WordPress translate APIs.
?>
<style type="text/css">
	.content__image {
		background-image: url(<?php echo esc_url( Opinionstage::get_instance()->plugin_url . '/admin/images/form-not-found.png' ); ?>);
		background-repeat: no-repeat;
		background-size: cover;
	}
</style>
<template data-opinionstage-content-popup-template>
	<div class='opinionstage-content-popup-contents
	<?php
	if ( ! $is_my_items_admin_page ) {
		echo 'opinionstage-content-popup-contents__edit-post'; }
	?>
	' data-opinionstage-content-popup data-opinionstage-client-logged-in="<?php echo esc_attr( $opinionstage_user_logged_in ); ?>">
		<?php
		require OPINIONSTAGE_PLUGIN_DIR . 'admin/template-parts/header-logo-line-logout-form.php';
		?>
		<header class='header'>
			<div class='header__container <?php echo $is_my_items_admin_page ? 'mw-1000' : ''; ?>'>
				<div class='header__action'>
					<?php if ( ! $is_my_items_admin_page ) { ?>
						<div class='btn-close' @click="closePopup">&#x2715;</div>
					<?php } ?>
				</div>
			</div>
		</header>
		<section>
			<popup-content
				:client-is-logged-in="isClientLoggedIn"
				:modal-is-opened="isModalOpened"
				:is-my-items-page="isMyItemsPage"
				:widget-type="widgetType"
				@widget-selected="selectWidgetAndExit"
				client-widgets-url="<?php echo esc_url( OPINIONSTAGE_CONTENT_POPUP_CLIENT_WIDGETS_API ); ?>"
				client-widgets-has-new-url="<?php echo esc_url( OPINIONSTAGE_CONTENT_POPUP_CLIENT_WIDGETS_API_RECENT_UPDATE ); ?>"
				access-key="<?php echo esc_js( Helper::get_user_access_token()); ?>"
				plugin-version="<?php echo esc_js( OPINIONSTAGE_WIDGET_VERSION ); ?>"
			>
			</popup-content>
		</section>
	</div>
</template>

<template id="opinionstage-widget-list">
	<?php
	TemplatesViewer::require_template('admin/template-parts/vue/widget-list', compact('is_my_items_admin_page'));
	?>
</template>

<template id="opinionstage-popup-content">
	<div v-if="clientIsLoggedIn">
		<div v-if="newWidgetsAvailable" class="notification-container">
			<notification v-on:update-btn-click='reloadAndRestartCheckingForUpdates'>
		</div>
		<div v-if="widgets == undefined">
			<p class="failed-load-items-request"><?php esc_html_e( 'An error occurred while loading the items.Try reloading the page. If the problem persists, please ', 'social-polls-by-opinionstage' ); ?>
				<a href="<?php echo esc_url( OPINIONSTAGE_LIVE_CHAT_URL_UTM ); ?>" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'contact our chat support.', 'social-polls-by-opinionstage' ); ?></a></p>
		</div>
		<template v-else>
			<div v-if="!dataLoading && isMyItemsPage && widgets !== undefined && widgets.length === 0 && searchCriteria.type === 'all' && searchCriteria.title === ''">
				<?php
				TemplatesViewer::require_template('admin/template-parts/vue/create-screen', compact('is_my_items_admin_page'));
				?>
			</div>
			<widget-list v-else
						:widgets='widgets'
						:pre-selected-widget-type='searchCriteria.type'
						:data-loading='dataLoading'
						:show-search='true'
						:no-more-data='noMoreData'
						@widget-selected="widgetSelected"
						@widgets-search-update='reloadData'
						@load-more-widgets='appendData'
			>
		</template>
	</div>
	<div class='page-content' v-else>
			<h1 class='main-title'>
				<?php esc_html_e( 'Connect WordPress with Opinion Stage to get started', 'social-polls-by-opinionstage' ); ?>
			</h1>
			<div class="opinionstage-text-center">
				<a id="os-start-login" data-os-login="" href="<?php echo esc_url( admin_url( 'admin.php?page=opinionstage-getting-started' ) ); ?>" class="opinionstage-button opinionstage-button__blue"><?php esc_html_e( 'Connect', 'social-polls-by-opinionstage' ); ?></a>
			</div>
	</div>
</template>

<template id="opinionstage-notification">
	<div class="opinionstage-section-notification__overlay">
		<div class="opinionstage-section-notification">
			<p class="opinionstage-section-notification__title">
				<?php esc_html_e( 'Your content has been updated, please click the button to update your view', 'social-polls-by-opinionstage' ); ?>
			</p>
			<div class="opinionstage-section-notification__controls">
				<button class='opinionstage-button opinionstage-button__blue' @click="initiateUpdate"><?php esc_html_e( 'Update view', 'social-polls-by-opinionstage' ); ?></button>
			</div>
		</div>
	</div>
</template>
