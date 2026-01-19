<?php
/**
 * ICS file handler class
 *
 * @package EvtCal_Add_To_Calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles ICS file generation and download
 */
class EvtCal_ICS_Handler {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'handle_ics_download'));
    }

    /**
     * Handle ICS file download request
     */
    public function handle_ics_download() {
        // Check if this is an ICS download request
        if (!isset($_GET['evtcal_ics_download'])) {
            return;
        }

        // Verify nonce for security
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'evtcal_ics_download')) {
            wp_die(esc_html__('Security check failed', 'evtcal-add-to-calendar'));
        }

        // Get and sanitize parameters
        $title = isset($_GET['title']) ? sanitize_text_field(wp_unslash($_GET['title'])) : 'Event';
        $desc  = isset($_GET['description']) ? sanitize_textarea_field(wp_unslash($_GET['description'])) : '';
        $loc   = isset($_GET['location']) ? sanitize_text_field(wp_unslash($_GET['location'])) : '';
        $start = isset($_GET['start']) ? sanitize_text_field(wp_unslash($_GET['start'])) : '';
        $end   = isset($_GET['end']) ? sanitize_text_field(wp_unslash($_GET['end'])) : '';
        $tz_string = isset($_GET['tz']) ? sanitize_text_field(wp_unslash($_GET['tz'])) : 'UTC';

        // Validate required parameters
        if (empty($start) || empty($end)) {
            wp_die(esc_html__('Invalid event parameters', 'evtcal-add-to-calendar'));
        }

        try {
            // Generate ICS content
            $ics_content = $this->generate_ics_content($title, $desc, $loc, $start, $end, $tz_string);

            // Send headers
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename="event.ics"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

            // Output ICS content - already escaped in generation
            echo $ics_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            exit;

        } catch (Exception $e) {
            wp_die(esc_html__('Error generating calendar file', 'evtcal-add-to-calendar'));
        }
    }

    /**
     * Generate ICS file content
     *
     * @param string $title Event title
     * @param string $desc Event description
     * @param string $loc Event location
     * @param string $start Start date/time
     * @param string $end End date/time
     * @param string $tz_string Timezone string
     * @return string ICS content
     */
    private function generate_ics_content($title, $desc, $loc, $start, $end, $tz_string) {
        // Create timezone object
        $tz = new DateTimeZone($tz_string);

        // Create DateTime objects
        $dtstart = new DateTime($start, $tz);
        $dtend   = new DateTime($end, $tz);

        // Convert to UTC
        $dtstart->setTimezone(new DateTimeZone('UTC'));
        $dtend->setTimezone(new DateTimeZone('UTC'));

        // Format for ICS
        $start_utc = $dtstart->format('Ymd\THis\Z');
        $end_utc   = $dtend->format('Ymd\THis\Z');
        $dtstamp   = gmdate('Ymd\THis\Z');

        // Escape special characters for ICS format
        $title = $this->escape_ics_string($title);
        $desc  = $this->escape_ics_string($desc);
        $loc   = $this->escape_ics_string($loc);

        // Generate unique ID
        $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : wp_parse_url(home_url(), PHP_URL_HOST);
        $uid = uniqid() . '@' . $host;

        // Build ICS content
        $ics = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Event Add to Calendar Plugin//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            "UID:{$uid}",
            "DTSTAMP:{$dtstamp}",
            "DTSTART:{$start_utc}",
            "DTEND:{$end_utc}",
            "SUMMARY:{$title}",
            "DESCRIPTION:{$desc}",
            "LOCATION:{$loc}",
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'END:VEVENT',
            'END:VCALENDAR'
        );

        return implode("\r\n", $ics);
    }

    /**
     * Escape string for ICS format
     *
     * @param string $str String to escape
     * @return string Escaped string
     */
    private function escape_ics_string($str) {
        // Replace special characters
        $str = str_replace(array("\r\n", "\n", "\r"), "\\n", $str);
        $str = str_replace(array(',', ';'), array('\\,', '\\;'), $str);
        return $str;
    }
}
