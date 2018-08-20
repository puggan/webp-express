<?php
/**
 * Plugin Name: WebP Express
 * Plugin URI: https://github.com/rosell-dk/webp-express
 * Description: Serve autogenerated WebP images instead of jpeg/png to browsers that supports WebP. Works on anything (media library images, galleries, theme images etc).
 * Version: 0.3.1
 * Author: Bjørn Rosell
 * Author URI: https://www.bitwise-it.dk
 * License: GPL2
 */


/*
Note: Perhaps create a plugin page on my website?, ie https://www.bitwise-it.dk/software/wordpress/webp-express
*/

/*
function tl_save_error() {
  update_option( 'plugin_error',  ob_get_contents() . empty(get_option('webp_express_max_quality')) ? 'empty' : 'not empty');
}
add_action( 'activated_plugin', 'tl_save_error' );
*/

define('WEBPEXPRESS_PLUGIN', __FILE__);
define('WEBPEXPRESS_PLUGIN_DIR', __DIR__);

add_action( 'admin_menu', function() {

    //Add Settings Page
    add_options_page(
        'WebP Express Settings', //Page Title
        __( 'WebP Express', 'yasr' ), //Menu Title
        'manage_options', //capability
        'webp_express_settings_page', //menu slug
        'webp_express_settings_page_content' //The function to be called to output the content for this page.
    );
});

include(plugin_dir_path(__FILE__) . 'lib/options.php');

if (get_option('webp-express-htaccess-needs-updating')) {
    delete_option('webp-express-htaccess-needs-updating');
    //include(plugin_dir_path(__FILE__) . 'lib/helpers.php');
    include_once 'lib/helpers.php';

    $rules = WebPExpressHelpers::generateHTAccessRules();
    WebPExpressHelpers::insertHTAccessRules($rules);

}


register_activation_hook(__FILE__, function () {
    include(plugin_dir_path(__FILE__) . 'lib/activate.php');
});

register_deactivation_hook(__FILE__, function () {
    include(plugin_dir_path(__FILE__) . 'lib/deactivate.php');
});

if (get_option('webp-express-message-pending')) {
    include(plugin_dir_path(__FILE__) . 'lib/message.php');
}

if (get_option('webp-express-deactivate')) {
    add_action('admin_init', function () {
        deactivate_plugins(plugin_basename(__FILE__));
    });
    delete_option('webp-express-deactivate');
}


// Add settings link on the plugins page
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=webp_express_settings_page' ) . '">Settings</a>',
    );
    return array_merge( $links, $mylinks );
});
