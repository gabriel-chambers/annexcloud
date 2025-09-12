<div class="opinionstage-hero-line">
	<img src="<?php echo esc_url( plugins_url( 'admin/images/logo.svg', OPINIONSTAGE_PLUGIN_FILE ) ); ?>" alt="<?php esc_html_e( 'Opinionstage', 'social-polls-by-opinionstage' ); ?>">
	<?php if ( ! empty( $user_email ) ) { ?>
		<div class="opinionstage-connectivity-status"><?php echo esc_html( $user_email ); ?>
			<form method="POST" action="<?php echo esc_url( get_admin_url( null, 'admin.php?page=' . OPINIONSTAGE_DISCONNECT_PAGE ) ); ?>" class="opinionstage-disconnect-form">
				<button class="opinionstage-button opinionstage-button__small" type="submit">Disconnect</button>
			</form>
		</div>
		<?php
	}
	?>
</div>