<div class="d4p-group d4p-group-dashboard-card d4p-group-dashboard-basic">
    <h3><?php esc_html_e( 'Features/Permissions Policy', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-stats">
        <ul class="gdsih-headers-overview">
			<?php

			$_data = gdsih_statistics()->permissions();

			foreach ( $_data as $header ) {
				?>
                <li style="<?php echo $header['css']; ?>">
                <strong><i class="fa fa-<?php echo $header['icon']; ?> fa-fw"></i> <?php echo $header['label']; ?></strong>
                <span class="active" style="background: <?php echo $header['color']; ?>"><?php echo $header['count']; ?></span>
                </li><?php
			}

			?>
        </ul>
        <div class="d4p-clearfix"></div>
    </div>
    <div class="d4p-group-inner">
        <h4><?php esc_html_e( 'Features/Permissions Policy Recommendation', 'gd-security-headers' ); ?></h4>

        <p>
			<?php esc_html_e( 'Here are few recommendations related to the policy rules you should you.', 'gd-security-headers' ); ?>
        </p>

        <ul class="gdsih-list-rec">
            <li><?php esc_html_e( 'It is highly recommended to set to \'Not Allowed\' rule for the \'Interest Cohort\', and with that disable Google tracking for \'FLoC\' or \'Federated Learning of Cohorts\'.', 'gd-security-headers' ); ?></li>
        </ul>
    </div>
    <div class="d4p-group-footer">
        <a href="admin.php?page=gd-security-headers-settings&panel=feature" class="button-primary"><?php esc_html_e( 'Policy Settings', 'gd-security-headers' ); ?></a>
    </div>
</div>