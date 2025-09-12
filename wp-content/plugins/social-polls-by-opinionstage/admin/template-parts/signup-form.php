<?php
/**
 * Signup Form
 *
 * @package OpinionStageWordPressPlugin
 */

use Opinionstage\Infrastructure\HelperUTM;
use Opinionstage\Infrastructure\SingleUseNonce;

$callback_url_with_nonce = SingleUseNonce::add_nonce_to_url( HelperUTM::callback_url() ) ?? HelperUTM::callback_url();

?>
<form action="<?php echo esc_attr( OPINIONSTAGE_LOGIN_PATH ); ?>" method="get" class="opinionstage-connect-form">
	<input type="hidden" name="utm_source" value="<?php echo esc_attr( OPINIONSTAGE_UTM_SOURCE ); ?>">
	<input type="hidden" name="utm_campaign" value="<?php echo esc_attr( OPINIONSTAGE_UTM_CAMPAIGN ); ?>">
	<input type="hidden" name="utm_medium" value="<?php echo esc_attr( OPINIONSTAGE_UTM_CONNECT_MEDIUM ); ?>">
	<input type="hidden" name="o" value="<?php echo esc_attr( OPINIONSTAGE_WIDGET_API_KEY ); ?>">
	<input type="hidden" name="callback" value="<?php echo esc_attr( $callback_url_with_nonce ); ?>">
	<button class="opinionstage-button opinionstage-button__blue" type="submit" name="intention" value="signup"><?php esc_html_e( 'Create Account', 'social-polls-by-opinionstage' ); ?></button>
	<button class="opinionstage-button" type="submit" name="intention" value="login"><?php esc_html_e( 'Login', 'social-polls-by-opinionstage' ); ?></button>
</form>
