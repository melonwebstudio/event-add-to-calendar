<?php
/**
 * Button template
 *
 * @package EvtCal_Add_To_Calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="evtcal">
    <button type="button" class="evtcal-btn btn btn-black" aria-haspopup="true" aria-expanded="false">
        <?php echo esc_html($atts['label']); ?> <span class="evtcal-arrow">▾</span>
    </button>
    <ul class="evtcal-dropdown" role="menu">
        <?php if (!empty($urls['google'])): ?>
        <li role="menuitem">
            <a href="<?php echo esc_url($urls['google']); ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url(EVTCAL_PLUGIN_URL . 'assets/images/google-calendar.png'); ?>"
                     alt="<?php esc_attr_e('Google Calendar', 'evtcal-add-to-calendar'); ?>"
                     width="20"
                     height="20">
                <?php esc_html_e('Google Calendar', 'evtcal-add-to-calendar'); ?>
            </a>
        </li>
        <?php endif; ?>

        <?php if (!empty($urls['office'])): ?>
        <li role="menuitem">
            <a href="<?php echo esc_url($urls['office']); ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url(EVTCAL_PLUGIN_URL . 'assets/images/office-365.webp'); ?>"
                     alt="<?php esc_attr_e('Office 365', 'evtcal-add-to-calendar'); ?>"
                     width="20"
                     height="20">
                <?php esc_html_e('Office 365', 'evtcal-add-to-calendar'); ?>
            </a>
        </li>
        <?php endif; ?>

        <?php if (!empty($urls['outlook_com'])): ?>
        <li role="menuitem">
            <a href="<?php echo esc_url($urls['outlook_com']); ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url(EVTCAL_PLUGIN_URL . 'assets/images/outlook.svg'); ?>"
                     alt="<?php esc_attr_e('Outlook.com', 'evtcal-add-to-calendar'); ?>"
                     width="20"
                     height="20">
                <?php esc_html_e('Outlook.com', 'evtcal-add-to-calendar'); ?>
            </a>
        </li>
        <?php endif; ?>

        <?php if (!empty($urls['yahoo'])): ?>
        <li role="menuitem">
            <a href="<?php echo esc_url($urls['yahoo']); ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url(EVTCAL_PLUGIN_URL . 'assets/images/yahoo-square-icon.webp'); ?>"
                     alt="<?php esc_attr_e('Yahoo Calendar', 'evtcal-add-to-calendar'); ?>"
                     width="20"
                     height="20">
                <?php esc_html_e('Yahoo Calendar', 'evtcal-add-to-calendar'); ?>
            </a>
        </li>
        <?php endif; ?>

        <?php if (!empty($urls['ics'])): ?>
        <li role="menuitem">
            <a href="<?php echo esc_url($urls['ics']); ?>">
                <img src="<?php echo esc_url(EVTCAL_PLUGIN_URL . 'assets/images/apple-calendar.svg'); ?>"
                     alt="<?php esc_attr_e('Apple Calendar', 'evtcal-add-to-calendar'); ?>"
                     width="20"
                     height="20">
                <?php esc_html_e('Apple / Outlook (.ics)', 'evtcal-add-to-calendar'); ?>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
