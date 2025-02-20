<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Ultimate Social Comments
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action( 'admin_notices', 'ufc_donate_admin_notice' );
add_action( 'admin_init', 'ufc_dismiss_donate_admin_notice' );

function ufc_donate_admin_notice() {
    // Show notice after 240 hours (10 days) from installed time.
    if ( ufc_plugin_installed_time_donate() > strtotime( '-360 hours' )
        || '1' === get_option( 'ufc_plugin_dismiss_donate_notice' )
        || ! current_user_can( 'manage_options' )
        || apply_filters( 'ufc_plugin_hide_sticky_donate_notice', false ) ) {
        return;
    }

    $dismiss = wp_nonce_url( add_query_arg( 'ufc_donate_notice_action', 'ufc_dismiss_donate_true' ), 'ufc_dismiss_donate_true' ); 
    $no_thanks = wp_nonce_url( add_query_arg( 'ufc_donate_notice_action', 'ufc_no_thanks_donate_true' ), 'ufc_no_thanks_donate_true' ); ?>
    
    <div class="notice notice-success">
        <p><?php _e( 'Hey, I noticed you\'ve been using Ultimate Social Comments for more than 2 week – that’s awesome! If you like Ultimate Social Comments and you are satisfied with the plugin, isn’t that worth a coffee or two? Please consider donating. Any amount is appreciated. Donations help me to continue support and development of this free plugin! Thank you very much!', 'ultimate-facebook-comments' ); ?></p>
        <p><a href="https://www.paypal.me/iamsayan" target="_blank" class="button button-secondary"><?php _e( 'Donate Now', 'ultimate-facebook-comments' ); ?></a>&nbsp;
        <a href="<?php echo $dismiss; ?>" class="already-did"><strong><?php _e( 'I already donated', 'ultimate-facebook-comments' ); ?></strong></a>&nbsp;<strong>|</strong>
        <a href="<?php echo $no_thanks; ?>" class="later"><strong><?php _e( 'Nope&#44; maybe later', 'ultimate-facebook-comments' ); ?></strong></a>&nbsp;<strong>|</strong>
        <a href="<?php echo $dismiss; ?>" class="dismiss"><strong><?php _e( 'I don\'t want to donate', 'ultimate-facebook-comments' ); ?></strong></a></p>
    </div>
<?php
}

function ufc_dismiss_donate_admin_notice() {

    if ( get_option( 'ufc_plugin_no_thanks_donate_notice' ) === '1' ) {
        if ( get_option( 'ufc_plugin_dismissed_time_donate' ) > strtotime( '-360 hours' ) ) {
            return;
        }
        delete_option( 'ufc_plugin_dismiss_donate_notice' );
        delete_option( 'ufc_plugin_no_thanks_donate_notice' );
    }

    if ( ! isset( $_GET['ufc_donate_notice_action'] ) ) {
        return;
    }

    if ( 'ufc_dismiss_donate_true' === $_GET['ufc_donate_notice_action'] ) {
        check_admin_referer( 'ufc_dismiss_donate_true' );
        update_option( 'ufc_plugin_dismiss_donate_notice', '1' );
    }

    if ( 'ufc_no_thanks_donate_true' === $_GET['ufc_donate_notice_action'] ) {
        check_admin_referer( 'ufc_no_thanks_donate_true' );
        update_option( 'ufc_plugin_no_thanks_donate_notice', '1' );
        update_option( 'ufc_plugin_dismiss_donate_notice', '1' );
        update_option( 'ufc_plugin_dismissed_time_donate', time() );
    }

    wp_redirect( remove_query_arg( 'ufc_donate_notice_action' ) );
    exit;
}

function ufc_plugin_installed_time_donate() {
    $installed_time = get_option( 'ufc_plugin_installed_time_donate' );
    if ( ! $installed_time ) {
        $installed_time = time();
        update_option( 'ufc_plugin_installed_time_donate', $installed_time );
    }
    return $installed_time;
}