<?php
/**
 * Plugin Name: Event - Add to Calendar
 * Plugin URI: https://wordpress.org/plugins/evtcal-add-to-calendar
 * Description: A powerful WordPress plugin to add calendar event buttons with support for Google Calendar, Outlook, Yahoo, Apple Calendar, and more.
 * Version: 1.0.0
 * Author: Melon Web Studio
 * Author URI: https://www.melonwebstudio.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: evtcal-add-to-calendar
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants - using EVTCAL prefix (6 chars)
define('EVTCAL_VERSION', '1.0.0');
define('EVTCAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EVTCAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EVTCAL_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class EvtCal_Add_To_Calendar {

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once EVTCAL_PLUGIN_DIR . 'includes/class-evtcal-settings.php';
        require_once EVTCAL_PLUGIN_DIR . 'includes/class-evtcal-shortcode.php';
        require_once EVTCAL_PLUGIN_DIR . 'includes/class-evtcal-ics-handler.php';
        require_once EVTCAL_PLUGIN_DIR . 'includes/class-evtcal-assets.php';
        require_once EVTCAL_PLUGIN_DIR . 'includes/class-evtcal-admin.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize settings
        new EvtCal_Settings();

        // Initialize shortcode
        new EvtCal_Shortcode();

        // Initialize ICS handler
        new EvtCal_ICS_Handler();

        // Initialize assets
        new EvtCal_Assets();

        // Initialize admin page
        if (is_admin()) {
            new EvtCal_Admin();
        }

        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Add plugin version to options
        update_option('evtcal_version', EVTCAL_VERSION);
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function evtcal_init_plugin() {
    return EvtCal_Add_To_Calendar::get_instance();
}

// Start the plugin
evtcal_init_plugin();
