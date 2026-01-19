<?php
/**
 * Settings handler class
 *
 * @package EvtCal_Add_To_Calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles plugin settings
 */
class EvtCal_Settings {

    /**
     * Settings option name
     */
    private $option_name = 'evtcal_settings';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'evtcal_settings_group',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
    }

    /**
     * Sanitize settings
     * IMPORTANT: Unchecked checkboxes don't send POST data, so we need to explicitly set them to 0
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        // Sanitize colors
        $sanitized['button_bg_color'] = isset($input['button_bg_color']) ? sanitize_hex_color($input['button_bg_color']) : '#000000';
        $sanitized['button_hover_color'] = isset($input['button_hover_color']) ? sanitize_hex_color($input['button_hover_color']) : '#333333';
        $sanitized['button_text_color'] = isset($input['button_text_color']) ? sanitize_hex_color($input['button_text_color']) : '#ffffff';

        // CRITICAL FIX: Explicitly set all calendar services to 0 first, then set to 1 if checked
        // This ensures unchecked boxes are saved as disabled (0) instead of keeping old value
        $sanitized['enable_google'] = isset($input['enable_google']) && $input['enable_google'] == '1' ? 1 : 0;
        $sanitized['enable_office365'] = isset($input['enable_office365']) && $input['enable_office365'] == '1' ? 1 : 0;
        $sanitized['enable_outlook'] = isset($input['enable_outlook']) && $input['enable_outlook'] == '1' ? 1 : 0;
        $sanitized['enable_yahoo'] = isset($input['enable_yahoo']) && $input['enable_yahoo'] == '1' ? 1 : 0;
        $sanitized['enable_ics'] = isset($input['enable_ics']) && $input['enable_ics'] == '1' ? 1 : 0;

        return $sanitized;
    }

    /**
     * Get settings with proper defaults
     */
    public static function get_settings() {
        $defaults = array(
            'button_bg_color' => '#000000',
            'button_hover_color' => '#333333',
            'button_text_color' => '#ffffff',
            'enable_google' => 1,
            'enable_office365' => 1,
            'enable_outlook' => 1,
            'enable_yahoo' => 1,
            'enable_ics' => 1,
        );

        $settings = get_option('evtcal_settings', array());

        // If settings exist in database, use them; otherwise use defaults
        if (!empty($settings)) {
            return $settings;
        }

        return $defaults;
    }

    /**
     * Get button colors CSS - returns escaped CSS
     */
    public static function get_button_colors_css() {
        $settings = self::get_settings();

        // Sanitize colors before building CSS
        $bg_color = sanitize_hex_color($settings['button_bg_color']);
        $hover_color = sanitize_hex_color($settings['button_hover_color']);
        $text_color = sanitize_hex_color($settings['button_text_color']);

        $css = "
        .evtcal-btn {
            background-color: {$bg_color} !important;
            color: {$text_color} !important;
        }
        .evtcal-btn:hover {
            background-color: {$hover_color} !important;
        }
        ";

        return $css;
    }
}
