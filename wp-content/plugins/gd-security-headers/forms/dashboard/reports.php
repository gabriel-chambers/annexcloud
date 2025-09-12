<div class="d4p-group d4p-group-dashboard-card d4p-group-dashboard-basic">
    <h3><?php esc_html_e( 'CSS and XXP reports logged in the past 7 days', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-stats">
        <ul>

			<?php

			$_data = gdsih_statistics()->get_reports_overview_week();

			foreach ( $_data as $component => $obj ) {
				$reports = $obj['reports'];

				if ( $obj['active'] ) {
					?>
                    <li><a href="admin.php?page=gd-security-headers-<?php echo $component; ?>-reports">
                        <strong><?php echo $obj['label']; ?></strong>
						<?php echo '<strong>' . $obj['reports'] . '</strong> ' . _n( 'report', 'reports', $reports, 'gd-security-headers' ); ?></a>
                    </li><?php
				} else {
					?>
                    <li><strong><?php echo $obj['label']; ?></strong>
					<?php echo '<strong>' . $obj['reports'] . '</strong> ' . _n( 'report', 'reports', $reports, 'gd-security-headers' ); ?>
                    </li><?php
				}

			}

			?>

        </ul>
        <div class="d4p-clearfix"></div>
    </div>
    <div class="d4p-group-inner">
        <h4><?php esc_html_e( 'Content Security Policy - Reports for URL\'s', 'gd-security-headers' ); ?></h4>

		<?php if ( ! $_data['csp']['active'] ) { ?>
            <p><?php esc_html_e( 'It is highly recommended to configure CSP security header with reporting enabled.', 'gd-security-headers' ); ?></p>
		<?php } else { ?>
			<?php

			$_list = gdsih_statistics()->get_csp_urls_week();

			if ( empty( $_list ) ) { ?>
                <p><?php esc_html_e( 'No reports logged in the past 7 days.', 'gd-security-headers' ); ?></p>
			<?php } else {

				?>
                <ul class="gdsih-security-reports">
					<?php

					foreach ( $_list as $obj ) {

						?>
                        <li>
                        <strong><?php echo $obj->url; ?></strong>
                        <a href="admin.php?page=gd-security-headers-csp-reports&s=<?php echo esc_attr( $obj->url ); ?>"><?php echo '<strong>' . $obj->reports . '</strong> ' . _n( 'report', 'reports', $obj->reports, 'gd-security-headers' ); ?></a>
                        </li><?php

					}

					?>
                </ul>
			<?php } ?>
		<?php } ?>

        <hr/>

        <h4><?php esc_html_e( 'X XSS Protection - Reports for URL\'s', 'gd-security-headers' ); ?></h4>

		<?php if ( ! $_data['xxp']['active'] ) { ?>
            <p><?php esc_html_e( 'It is highly recommended to configure X XSS Protection security header with reporting enabled.', 'gd-security-headers' ); ?></p>
		<?php } else { ?>
			<?php

			$_list = gdsih_statistics()->get_xxp_urls_week();

			if ( empty( $_list ) ) { ?>
                <p><?php esc_html_e( 'No reports logged in the past 7 days.', 'gd-security-headers' ); ?></p>
			<?php } else {

				?>
                <ul class="gdsih-security-reports">
					<?php

					foreach ( $_list as $obj ) {

						?>
                        <li>
                        <strong><?php echo $obj->url; ?></strong>
                        <a href="admin.php?page=gd-security-headers-xxp-reports&s=<?php echo esc_attr( $obj->url ); ?>"><?php echo '<strong>' . $obj->reports . '</strong> ' . _n( 'report', 'reports', $obj->reports, 'gd-security-headers' ); ?></a>
                        </li><?php

					}

					?>
                </ul>
			<?php } ?>
		<?php } ?>
    </div>
    <div class="d4p-group-footer">
		<?php if ( $_data['csp']['active'] ) { ?>
            <a href="admin.php?page=gd-security-headers-csp-reports" class="button-primary"><?php esc_html_e( 'All CSP reports', 'gd-security-headers' ); ?></a>
		<?php }
		if ( $_data['xxp']['active'] ) { ?>
            <a href="admin.php?page=gd-security-headers-xxp-reports" class="button-primary"><?php esc_html_e( 'All XXP reports', 'gd-security-headers' ); ?></a>
		<?php } ?>
    </div>
</div>