<?php
// Including helper functions
require_once 'helper.php';

add_action('admin_menu', 'facetwp_settings_menu', 99);

function facetwp_settings_menu()
{
    add_submenu_page(
        'options-general.php',
        'FacetWP Additional Settings',
        'FacetWP Additional Settings',
        'administrator',
        'facetwp-layout-settings',
        'facetwp_additional_settings_page'
    );
    add_action('admin_init', 'register_facetwp_additional_settings');
}

function register_facetwp_additional_settings()
{
    register_setting('facetwp-layout-settings-group', 'facetwp_column_count');
    register_setting('facetwp-layout-settings-group', 'facetwp_exclude_featured');
    register_setting('facetwp-layout-settings-group', 'facetwp_additional_class');
}

function facetwp_additional_settings_page()
{
    $facetwp_column_count = esc_attr(get_option('facetwp_column_count'));
    $facetwp_exclude_featured = esc_attr(get_option('facetwp_exclude_featured'));
    $facetwp_additional_class = esc_attr(get_option('facetwp_additional_class'));
?>
    <div class="wrap">
        <h2><?php echo  __("FacetWP Templates Additional Settings", "e25-facetwp-settings") ?></h2>
        <hr />
        <form method="post" action="options.php">
            <?php settings_fields('facetwp-layout-settings-group'); ?>
            <?php do_settings_sections('facetwp-layout-settings-group'); ?>
            <table class="form-table">
                <caption></caption>
                <tr>
                    <th scope="row"><?php echo  __("Number of columns", "e25-facetwp-settings") ?></th>
                    <td>
                        <input
                            type="number"
                            placeholder="3"
                            min="1"
                            max="4"
                            name="facetwp_column_count"
                            value="<?php echo $facetwp_column_count; ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php echo  __("Exclude featured posts from all templates", "e25-facetwp-settings") ?>
                    </th>
                    <td>
                        <input
                            type="checkbox"
                            name="facetwp_exclude_featured"
                            <?php echo $facetwp_exclude_featured == "on" ? "checked" : "" ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row" rowspan="2">
                        <?php echo  __("Additional classes for the template wrapper", "e25-facetwp-settings") ?>
                    </th>
                    <td>
                        <input
                            type="text"
                            placeholder="<?php echo  __("Ex: class1,class2", "e25-facetwp-settings") ?>"
                            name="facetwp_additional_class"
                            value="<?php echo $facetwp_additional_class; ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php } ?>
