<?php
/**
 * Shortcode related functions
 *
 * @package OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

function opinionstage_enqueue_shortcodes_assets() {
	opinionstage_register_javascript_asset( 'shortcodes', 'shortcodes.js', array( 'jquery' ) );

	opinionstage_enqueue_js_asset( 'shortcodes' );
}

function opinionstage_poll_or_set_shortcode( $atts ) {
	if ( is_feed() ) {
		return __( "Note: There is a poll embedded within this post, please visit the site to participate in this post's poll.", 'social-polls-by-opinionstage' );
	} else {
		$shortcode_params = shortcode_atts(
			array(
				'id'    => 0,
				'type'  => 'poll',
				'width' => '',
			),
			$atts,
			OPINIONSTAGE_POLL_SHORTCODE
		);

		$id    = intval( $shortcode_params['id'] );
		$type  = $shortcode_params['type'];
		$width = $shortcode_params['width'];

		return opinionstage_widget_embedded( opinionstage_poll_or_set_embed_code_url( $id, $type, $width ) );
	}
}

function opinionstage_widget_shortcode( $atts ) {
	if ( is_feed() ) {
		return __( "Note: There is a widget embedded within this post, please visit the site to participate in this post's widget.", 'social-polls-by-opinionstage' );
	} else {
		$shortcode_params = shortcode_atts(
			array(
				'path'            => 0,
				'width'           => '',
			),
			$atts,
			OPINIONSTAGE_WIDGET_SHORTCODE
		);

		$path            = $shortcode_params['path'];
		$width           = $shortcode_params['width'];

		return opinionstage_widget_embedded( opinionstage_widget_embed_code_url( $path, $width ) );
	}
}


function opinionstage_poll_or_set_embed_code_url( $id, $type, $width ) {
	if ( ! empty( $id ) ) {
		if ( $type == 'set' ) {
			$embed_code_url = OPINIONSTAGE_API_PATH . '/sets/' . $id . '/code.json';
		} else {
			$embed_code_url = OPINIONSTAGE_API_PATH . '/polls/' . $id . '/code.json?width=' . $width;
		}

		if ( is_home() ) {
			$embed_code_url .= '?h=1';
		}

		return $embed_code_url;
	}
}

function opinionstage_widget_embed_code_url( $path, $width ) {
	if ( isset( $path ) && ! empty( $path ) ) {
        return OPINIONSTAGE_API_PATH . '/widgets' . $path . '/code.json?width=' . $width;
	}
}


function opinionstage_widget_embedded( $url ) {
	return sprintf( '<div data-opinionstage-embed-url="%s" style="display: none; visibility: hidden;"></div>', esc_url( $url ) );
}
