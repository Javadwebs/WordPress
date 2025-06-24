<?php
/**
 * Plugin Name: Modern AI Chat for Elementor
 * Description: Adds an AI-powered chat widget to Elementor.
 * Version: 1.0.0
 * Author: Jules for AI
 * Author URI: https://example.com
 * Plugin URI: https://example.com/modern-ai-chat-elementor
 * Text Domain: modern-ai-chat-elementor
 * Domain Path: /languages
 * Elementor tested up to: 3.15.0
 * Elementor Pro tested up to: 3.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MODERN_AI_CHAT_ELEMENTOR_VERSION', '1.0.0' );
define( 'MODERN_AI_CHAT_ELEMENTOR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MODERN_AI_CHAT_ELEMENTOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin textdomain.
 */
function modern_ai_chat_elementor_load_textdomain() {
	load_plugin_textdomain( 'modern-ai-chat-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'modern_ai_chat_elementor_load_textdomain' );


/**
 * Register Elementor custom category and widgets.
 */
function modern_ai_chat_elementor_register_widgets( $widgets_manager ) {
    // Register Custom Category
    \Elementor\Plugin::$instance->elements_manager->add_category(
        'ai-widgets',
        [
            'title' => esc_html__( 'AI Widgets', 'modern-ai-chat-elementor' ),
            'icon' => 'eicon-chat',
        ]
    );

	require_once( MODERN_AI_CHAT_ELEMENTOR_PLUGIN_PATH . 'widgets/ai-chat-widget.php' );
	$widgets_manager->register( new \ModernAIChatElementor\Widgets\ModernChatWidget() );

}
add_action( 'elementor/widgets/register', 'modern_ai_chat_elementor_register_widgets' );


/**
 * Enqueue widget scripts and styles.
 */
function modern_ai_chat_elementor_enqueue_assets() {
    // Styles
    wp_register_style(
        'modern-ai-chat-widget-style',
        MODERN_AI_CHAT_ELEMENTOR_PLUGIN_URL . 'assets/css/chat-widget.css',
        [],
        MODERN_AI_CHAT_ELEMENTOR_VERSION
    );

    // Scripts
    wp_register_script(
        'modern-ai-chat-widget-script',
        MODERN_AI_CHAT_ELEMENTOR_PLUGIN_URL . 'assets/js/chat-widget.js',
        [ 'jquery' ], // Add jquery as a dependency
        MODERN_AI_CHAT_ELEMENTOR_VERSION,
        true // Load in footer
    );

    wp_localize_script(
        'modern-ai-chat-widget-script',
        'modernAIChat',
        [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'modern_ai_chat_nonce' ),
            'dev_webhook_url' => 'https://n8n-xtgtmcqc.eu-central-1.clawcloudrun.com/webhook/805766d2-1608-419d-8fc7-a161fe41ac05' // For development
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'modern_ai_chat_elementor_enqueue_assets' );
add_action( 'elementor/editor/after_enqueue_scripts', 'modern_ai_chat_elementor_enqueue_assets' ); // For editor


/**
 * AJAX handler for sending messages.
 */
function modern_ai_chat_elementor_send_message() {
    check_ajax_referer( 'modern_ai_chat_nonce', 'nonce' );

    $message = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';
    $session_id = isset( $_POST['sessionId'] ) ? sanitize_text_field( $_POST['sessionId'] ) : '';
    $webhook_url = isset( $_POST['webhook_url'] ) ? esc_url_raw( $_POST['webhook_url'] ) : '';

    if ( empty( $message ) || empty( $session_id ) || empty( $webhook_url ) ) {
        wp_send_json_error( [ 'message' => __( 'Missing required fields.', 'modern-ai-chat-elementor' ) ] );
        return;
    }

    // Validate Webhook URL
    if (filter_var($webhook_url, FILTER_VALIDATE_URL) === FALSE) {
        wp_send_json_error( [ 'message' => __( 'Invalid Webhook URL provided.', 'modern-ai-chat-elementor' ) ] );
        return;
    }

    $response = wp_remote_post( $webhook_url, [
        'method'    => 'POST',
        'timeout'   => 45,
        'redirection' => 5,
        'blocking'  => true,
        'headers'   => [ 'Content-Type' => 'application/json; charset=utf-array' ],
        'body'      => json_encode( [ 'message' => $message, 'sessionId' => $session_id ] ),
        'cookies'   => []
    ] );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => sprintf(__( 'Failed to connect to webhook: %s', 'modern-ai-chat-elementor' ), $error_message) ] );
    } else {
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() === JSON_ERROR_NONE && isset($data) ) {
            // Assuming the webhook returns something like { "response": "Bot's answer" }
            // Or simply the text response directly. Adjust as needed.
            wp_send_json_success( $data );
        } else {
            // If the response is not JSON or malformed, send the raw body if it's likely text.
            // Or send an error if it's critical that the response is JSON.
            // For now, let's assume the webhook might send plain text or a specific JSON structure.
            // This part needs to be robust based on expected webhook responses.
             wp_send_json_success( [ 'response' => $body ] ); // Simplistic, adapt if webhook sends structured JSON
        }
    }
    wp_die();
}
add_action( 'wp_ajax_send_chat_message', 'modern_ai_chat_elementor_send_message' );
add_action( 'wp_ajax_nopriv_send_chat_message', 'modern_ai_chat_elementor_send_message' );

/**
 * Check if Elementor is active.
 */
function modern_ai_chat_elementor_init() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'modern_ai_chat_elementor_admin_notice_missing_main_plugin' );
        return;
    }
}
add_action( 'plugins_loaded', 'modern_ai_chat_elementor_init' );

/**
 * Admin notice for missing Elementor plugin.
 */
function modern_ai_chat_elementor_admin_notice_missing_main_plugin() {
    if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
    $message = sprintf(
        esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'modern-ai-chat-elementor' ),
        '<strong>' . esc_html__( 'Modern AI Chat for Elementor', 'modern-ai-chat-elementor' ) . '</strong>',
        '<strong>' . esc_html__( 'Elementor', 'modern-ai-chat-elementor' ) . '</strong>'
    );
    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}

// Placeholder for future activation/deactivation hooks
function modern_ai_chat_elementor_activate() {
    // Actions to run on plugin activation
}
register_activation_hook( __FILE__, 'modern_ai_chat_elementor_activate' );

function modern_ai_chat_elementor_deactivate() {
    // Actions to run on plugin deactivation
}
register_deactivation_hook( __FILE__, 'modern_ai_chat_elementor_deactivate' );
