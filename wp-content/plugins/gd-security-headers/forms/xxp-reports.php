<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include( GDSIH_PATH . 'forms/shared/top.php' );

?>

    <div class="d4p-content-right d4p-content-full">
        <form method="get" action="">
            <input type="hidden" name="page" value="gd-security-toolbox-xxp-reports"/>
            <input type="hidden" value="getback" name="gdsih_handler"/>

			<?php

			require_once( GDSIH_PATH . 'core/grids/xxp.php' );

			$_grid = new gdsih_xxp_report_grid();
			$_grid->prepare_items();

			$_grid->search_box( __( 'Search', 'gd-security-headers' ), 's' );
			$_grid->display();

			?>
        </form>
    </div>

<?php

include( GDSIH_PATH . 'forms/shared/bottom.php' );
include( GDSIH_PATH . 'forms/shared/dialogs.php' );
