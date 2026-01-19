<?php
/**
 * Shortcode handler class
 *
 * @package EvtCal_Add_To_Calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles the add to calendar shortcode
 */
class EvtCal_Shortcode {

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('evtcal_add_to_calendar', array($this, 'render_button'));
    }

    /**
     * Render the add to calendar button
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_button($atts) {
        // Parse shortcode attributes
        $atts = shortcode_atts(array(
            'title'       => 'My Event',
            'description' => 'Join us for an amazing event',
            'location'    => 'Event Venue',
            'start'       => '2025-11-15 10:00:00',
            'end'         => '2025-11-15 12:00:00',
            'timezone'    => 'America/Los_Angeles',
            'label'       => 'Add to Calendar',
        ), $atts, 'evtcal_add_to_calendar');

        // Sanitize inputs
        $atts = array_map('sanitize_text_field', $atts);

        // Get settings to filter enabled services
        $settings = EvtCal_Settings::get_settings();

        // Get calendar URLs
        $all_urls = $this->generate_calendar_urls($atts);

        // Filter URLs based on enabled services
        $urls = $this->filter_enabled_services($all_urls, $settings);

        // If no services are enabled, show a message instead
        if (empty($urls)) {
            if (current_user_can('manage_options')) {
                return '<div style="padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                    <strong>' . esc_html__('Add to Calendar:', 'evtcal-add-to-calendar') . '</strong> ' . esc_html__('No calendar services are enabled. Please enable at least one service in the plugin settings.', 'evtcal-add-to-calendar') . '
                </div>';
            }
            return ''; // Don't show anything to regular users if no services enabled
        }

        // Generate output
        ob_start();
        include EVTCAL_PLUGIN_DIR . 'templates/button-template.php';
        return ob_get_clean();
    }

    /**
     * Generate calendar URLs for all services
     *
     * @param array $atts Event attributes
     * @return array Calendar URLs
     */
    private function generate_calendar_urls($atts) {
        try {
            // Create timezone object
            $tz = new DateTimeZone($atts['timezone']);

            // Create DateTime objects
            $start = new DateTime($atts['start'], $tz);
            $end   = new DateTime($atts['end'], $tz);

            // Convert to UTC
            $start_utc = clone $start;
            $start_utc->setTimezone(new DateTimeZone('UTC'));
            $end_utc = clone $end;
            $end_utc->setTimezone(new DateTimeZone('UTC'));

            // Format dates for different services
            $start_str = $start_utc->format('Ymd\THis\Z');
            $end_str   = $end_utc->format('Ymd\THis\Z');
            $start_iso = $start_utc->format('Y-m-d\TH:i:s\Z');
            $end_iso   = $end_utc->format('Y-m-d\TH:i:s\Z');

            // URL encode parameters
            $title = rawurlencode($atts['title']);
            $desc  = rawurlencode($atts['description']);
            $loc   = rawurlencode($atts['location']);

            // Build calendar service URLs
            return array(
                'google' => "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$start_str}/{$end_str}&details={$desc}&location={$loc}",
                'yahoo'  => "https://calendar.yahoo.com/?v=60&TITLE={$title}&ST={$start_str}&ET={$end_str}&DESC={$desc}&in_loc={$loc}",
                'office' => "https://outlook.office.com/calendar/0/deeplink/compose?subject={$title}&body={$desc}&startdt={$start_iso}&enddt={$end_iso}&location={$loc}",
                'outlook_com' => "https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent&subject={$title}&body={$desc}&startdt={$start_iso}&enddt={$end_iso}&location={$loc}",
                'ics' => $this->get_ics_download_url($atts)
            );

        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Generate ICS download URL
     *
     * @param array $atts Event attributes
     * @return string ICS download URL
     */
    private function get_ics_download_url($atts) {
        return add_query_arg(array(
            'evtcal_ics_download' => 1,
            'title'        => urlencode($atts['title']),
            'description'  => urlencode($atts['description']),
            'location'     => urlencode($atts['location']),
            'start'        => urlencode($atts['start']),
            'end'          => urlencode($atts['end']),
            'tz'           => urlencode($atts['timezone']),
            '_wpnonce'     => wp_create_nonce('evtcal_ics_download')
        ), home_url('/'));
    }

    /**
     * Filter calendar services based on settings
     * CRITICAL: This checks if services are enabled (value == 1) in settings
     *
     * @param array $urls All calendar URLs
     * @param array $settings Plugin settings
     * @return array Filtered URLs
     */
    private function filter_enabled_services($urls, $settings) {
        $filtered = array();

        // Check each service - only add if explicitly enabled (== 1)
        if (isset($settings['enable_google']) && $settings['enable_google'] == 1 && !empty($urls['google'])) {
            $filtered['google'] = $urls['google'];
        }

        if (isset($settings['enable_office365']) && $settings['enable_office365'] == 1 && !empty($urls['office'])) {
            $filtered['office'] = $urls['office'];
        }

        if (isset($settings['enable_outlook']) && $settings['enable_outlook'] == 1 && !empty($urls['outlook_com'])) {
            $filtered['outlook_com'] = $urls['outlook_com'];
        }

        if (isset($settings['enable_yahoo']) && $settings['enable_yahoo'] == 1 && !empty($urls['yahoo'])) {
            $filtered['yahoo'] = $urls['yahoo'];
        }

        if (isset($settings['enable_ics']) && $settings['enable_ics'] == 1 && !empty($urls['ics'])) {
            $filtered['ics'] = $urls['ics'];
        }

        return $filtered;
    }
}
