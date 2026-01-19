<?php
/**
 * Assets handler class
 *
 * @package EvtCal_Add_To_Calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles CSS and JavaScript assets
 */
class EvtCal_Assets {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue plugin styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'evtcal-styles',
            EVTCAL_PLUGIN_URL . 'assets/css/evtcal-styles.css',
            array(),
            EVTCAL_VERSION,
            'all'
        );

        // Add custom colors inline - CSS is already sanitized in get_button_colors_css()
        $custom_css = EvtCal_Settings::get_button_colors_css();
        wp_add_inline_style('evtcal-styles', $custom_css);
    }

    /**
     * Enqueue plugin scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'evtcal-scripts',
            EVTCAL_PLUGIN_URL . 'assets/js/evtcal-scripts.js',
            array(),
            EVTCAL_VERSION,
            true
        );
    }
}
