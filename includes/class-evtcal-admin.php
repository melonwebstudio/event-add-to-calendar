<?php
/**
 * Admin page handler class
 *
 * @package EvtCal_Add_To_Calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles admin page functionality
 */
class EvtCal_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'handle_settings_save'));
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Event Add to Calendar', 'evtcal-add-to-calendar'),
            __('Add to Calendar', 'evtcal-add-to-calendar'),
            'manage_options',
            'event-add-to-calendar',
            array($this, 'render_admin_page'),
            'dashicons-calendar-alt',
            30
        );
    }

    /**
     * Handle settings save
     */
    public function handle_settings_save() {
        // Check if form is submitted
        if (!isset($_POST['evtcal_save_settings'])) {
            return;
        }

        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'evtcal_settings_nonce')) {
            return;
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            return;
        }

        // Prepare settings array
        // CRITICAL: Set all checkboxes to 0 first, then to 1 if checked
        $new_settings = array(
            'button_bg_color' => sanitize_hex_color(wp_unslash($_POST['button_bg_color'] ?? '#000000')),
            'button_hover_color' => sanitize_hex_color(wp_unslash($_POST['button_hover_color'] ?? '#333333')),
            'button_text_color' => sanitize_hex_color(wp_unslash($_POST['button_text_color'] ?? '#ffffff')),
            'enable_google' => isset($_POST['enable_google']) ? 1 : 0,
            'enable_office365' => isset($_POST['enable_office365']) ? 1 : 0,
            'enable_outlook' => isset($_POST['enable_outlook']) ? 1 : 0,
            'enable_yahoo' => isset($_POST['enable_yahoo']) ? 1 : 0,
            'enable_ics' => isset($_POST['enable_ics']) ? 1 : 0,
        );

        // Save to database
        update_option('evtcal_settings', $new_settings);

        // Redirect to prevent form resubmission
        wp_safe_redirect(add_query_arg(array(
            'page' => 'event-add-to-calendar',
            'settings-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our admin page
        if ('toplevel_page_event-add-to-calendar' !== $hook) {
            return;
        }

        // Enqueue WordPress color picker
        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            'evtcal-admin-styles',
            EVTCAL_PLUGIN_URL . 'assets/css/evtcal-admin.css',
            array('wp-color-picker'),
            EVTCAL_VERSION
        );

        wp_enqueue_script(
            'evtcal-admin-scripts',
            EVTCAL_PLUGIN_URL . 'assets/js/evtcal-admin.js',
            array('jquery', 'wp-color-picker'),
            EVTCAL_VERSION,
            true
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        // Get current settings
        $settings = EvtCal_Settings::get_settings();

        // Show success message
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        $settings_updated = isset($_GET['settings-updated']) ? sanitize_text_field(wp_unslash($_GET['settings-updated'])) : '';

        if ($settings_updated === 'true' && wp_verify_nonce($nonce, 'evtcal_settings_updated')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Settings saved successfully!', 'evtcal-add-to-calendar'); ?></p>
            </div>
            <?php
        }
        ?>
        <div class="wrap evtcal-admin-wrap">
            <h1>
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php esc_html_e('Event Add to Calendar', 'evtcal-add-to-calendar'); ?>
            </h1>

            <div class="evtcal-admin-header">
                <p class="evtcal-version">
                   <?php echo esc_html__('Version', 'evtcal-add-to-calendar') . ' ' . esc_html(EVTCAL_VERSION); ?>
                </p>
            </div>

            <!-- Tabs Navigation -->
            <div class="evtcal-tabs">
                <button class="evtcal-tab-btn active" data-tab="documentation">
                    <span class="dashicons dashicons-book"></span>
                    <?php esc_html_e('Documentation', 'evtcal-add-to-calendar'); ?>
                </button>
                <button class="evtcal-tab-btn" data-tab="settings">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php esc_html_e('Settings', 'evtcal-add-to-calendar'); ?>
                </button>
            </div>

            <div class="evtcal-admin-content">

                <!-- Documentation Tab -->
                <div class="evtcal-tab-content active" id="tab-documentation">

                    <!-- Welcome Section -->
                    <div class="evtcal-card evtcal-welcome">
                        <h2><?php esc_html_e('Welcome to Event Add to Calendar!', 'evtcal-add-to-calendar'); ?></h2>
                        <p><?php esc_html_e('Thank you for installing Event Add to Calendar plugin. This powerful tool allows your visitors to easily add events to their preferred calendar service.', 'evtcal-add-to-calendar'); ?></p>
                        <div class="evtcal-features">
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <span><?php esc_html_e('Multiple Calendar Services', 'evtcal-add-to-calendar'); ?></span>
                            </div>
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <span><?php esc_html_e('Easy Shortcode Implementation', 'evtcal-add-to-calendar'); ?></span>
                            </div>
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <span><?php esc_html_e('Fully Responsive Design', 'evtcal-add-to-calendar'); ?></span>
                            </div>
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <span><?php esc_html_e('Customizable Colors', 'evtcal-add-to-calendar'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Start Section -->
                    <div class="evtcal-card">
                        <h2>
                            <span class="dashicons dashicons-controls-play"></span>
                            <?php esc_html_e('Quick Start Guide', 'evtcal-add-to-calendar'); ?>
                        </h2>
                        <div class="evtcal-steps">
                            <div class="step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h3><?php esc_html_e('Configure Settings', 'evtcal-add-to-calendar'); ?></h3>
                                    <p><?php esc_html_e('Go to Settings tab to customize button colors and enable/disable calendar services.', 'evtcal-add-to-calendar'); ?></p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h3><?php esc_html_e('Copy the Shortcode', 'evtcal-add-to-calendar'); ?></h3>
                                    <p><?php esc_html_e('Copy the basic shortcode or customize it with your event details.', 'evtcal-add-to-calendar'); ?></p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h3><?php esc_html_e('Add to Your Page', 'evtcal-add-to-calendar'); ?></h3>
                                    <p><?php esc_html_e('Paste the shortcode into any post, page, or widget area.', 'evtcal-add-to-calendar'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Shortcode Section -->
                    <div class="evtcal-card">
                        <h2>
                            <span class="dashicons dashicons-editor-code"></span>
                            <?php esc_html_e('Basic Shortcode', 'evtcal-add-to-calendar'); ?>
                        </h2>
                        <p><?php esc_html_e('Use this shortcode with default settings:', 'evtcal-add-to-calendar'); ?></p>
                        <div class="evtcal-code-block">
                            <code>[evtcal_add_to_calendar]</code>
                            <button class="evtcal-copy-btn" data-copy="[evtcal_add_to_calendar]">
                                <span class="dashicons dashicons-clipboard"></span>
                                <?php esc_html_e('Copy', 'evtcal-add-to-calendar'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Custom Shortcode Section -->
                    <div class="evtcal-card">
                        <h2>
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php esc_html_e('Customized Shortcode', 'evtcal-add-to-calendar'); ?>
                        </h2>
                        <p><?php esc_html_e('Customize all attributes to match your event:', 'evtcal-add-to-calendar'); ?></p>
                        <div class="evtcal-code-block">
                            <code>[evtcal_add_to_calendar
    title="My Awesome Event"
    description="Join us for an amazing session"
    location="Conference Center, New York, NY"
    start="2025-07-15 14:00:00"
    end="2025-07-15 16:00:00"
    timezone="America/New_York"
    label="Save the Date"]</code>
                            <button class="evtcal-copy-btn" data-copy='[evtcal_add_to_calendar title="My Awesome Event" description="Join us for an amazing session" location="Conference Center, New York, NY" start="2025-07-15 14:00:00" end="2025-07-15 16:00:00" timezone="America/New_York" label="Save the Date"]'>
                                <span class="dashicons dashicons-clipboard"></span>
                                <?php esc_html_e('Copy', 'evtcal-add-to-calendar'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Shortcode Attributes Section -->
                    <div class="evtcal-card">
                        <h2>
                            <span class="dashicons dashicons-list-view"></span>
                            <?php esc_html_e('Shortcode Attributes', 'evtcal-add-to-calendar'); ?>
                        </h2>
                        <p><?php esc_html_e('All available attributes and their default values:', 'evtcal-add-to-calendar'); ?></p>
                        <table class="evtcal-attributes-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Attribute', 'evtcal-add-to-calendar'); ?></th>
                                    <th><?php esc_html_e('Description', 'evtcal-add-to-calendar'); ?></th>
                                    <th><?php esc_html_e('Default Value', 'evtcal-add-to-calendar'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>title</code></td>
                                    <td><?php esc_html_e('Event title', 'evtcal-add-to-calendar'); ?></td>
                                    <td><?php esc_html_e('My Event', 'evtcal-add-to-calendar'); ?></td>
                                </tr>
                                <tr>
                                    <td><code>description</code></td>
                                    <td><?php esc_html_e('Event description', 'evtcal-add-to-calendar'); ?></td>
                                    <td><?php esc_html_e('Join us for an amazing event', 'evtcal-add-to-calendar'); ?></td>
                                </tr>
                                <tr>
                                    <td><code>location</code></td>
                                    <td><?php esc_html_e('Event location', 'evtcal-add-to-calendar'); ?></td>
                                    <td><?php esc_html_e('Event Venue', 'evtcal-add-to-calendar'); ?></td>
                                </tr>
                                <tr>
                                    <td><code>start</code></td>
                                    <td><?php esc_html_e('Start date/time (YYYY-MM-DD HH:MM:SS)', 'evtcal-add-to-calendar'); ?></td>
                                    <td>2025-11-15 10:00:00</td>
                                </tr>
                                <tr>
                                    <td><code>end</code></td>
                                    <td><?php esc_html_e('End date/time (YYYY-MM-DD HH:MM:SS)', 'evtcal-add-to-calendar'); ?></td>
                                    <td>2025-11-15 12:00:00</td>
                                </tr>
                                <tr>
                                    <td><code>timezone</code></td>
                                    <td><?php esc_html_e('PHP timezone identifier', 'evtcal-add-to-calendar'); ?></td>
                                    <td>America/Los_Angeles</td>
                                </tr>
                                <tr>
                                    <td><code>label</code></td>
                                    <td><?php esc_html_e('Button text', 'evtcal-add-to-calendar'); ?></td>
                                    <td><?php esc_html_e('Add to Calendar', 'evtcal-add-to-calendar'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- Settings Tab -->
                <div class="evtcal-tab-content" id="tab-settings">

                    <form method="post" action="">
                        <?php wp_nonce_field('evtcal_settings_nonce'); ?>

                        <!-- Current Settings Debug (Remove in production) -->
                        <div class="evtcal-card" style="background: #f0f0f1; border-left: 4px solid #2271b1;">
                            <h3><?php esc_html_e('Current Settings Status', 'evtcal-add-to-calendar'); ?></h3>
                            <table class="widefat">
                                <tr>
                                    <th><?php esc_html_e('Google Calendar:', 'evtcal-add-to-calendar'); ?></th>
                                    <td><strong><?php echo esc_html($settings['enable_google']) == 1 ? '✅ ENABLED' : '❌ DISABLED'; ?></strong> (<?php esc_html_e('Value:', 'evtcal-add-to-calendar'); ?> <?php echo esc_html($settings['enable_google']); ?>)</td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Office 365:', 'evtcal-add-to-calendar'); ?></th>
                                    <td><strong><?php echo esc_html($settings['enable_office365']) == 1 ? '✅ ENABLED' : '❌ DISABLED'; ?></strong> (<?php esc_html_e('Value:', 'evtcal-add-to-calendar'); ?> <?php echo esc_html($settings['enable_office365']); ?>)</td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Outlook.com:', 'evtcal-add-to-calendar'); ?></th>
                                    <td><strong><?php echo esc_html($settings['enable_outlook']) == 1 ? '✅ ENABLED' : '❌ DISABLED'; ?></strong> (<?php esc_html_e('Value:', 'evtcal-add-to-calendar'); ?> <?php echo esc_html($settings['enable_outlook']); ?>)</td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Yahoo Calendar:', 'evtcal-add-to-calendar'); ?></th>
                                    <td><strong><?php echo esc_html($settings['enable_yahoo']) == 1 ? '✅ ENABLED' : '❌ DISABLED'; ?></strong> (<?php esc_html_e('Value:', 'evtcal-add-to-calendar'); ?> <?php echo esc_html($settings['enable_yahoo']); ?>)</td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Apple Calendar:', 'evtcal-add-to-calendar'); ?></th>
                                    <td><strong><?php echo esc_html($settings['enable_ics']) == 1 ? '✅ ENABLED' : '❌ DISABLED'; ?></strong> (<?php esc_html_e('Value:', 'evtcal-add-to-calendar'); ?> <?php echo esc_html($settings['enable_ics']); ?>)</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Button Colors Section -->
                        <div class="evtcal-card">
                            <h2>
                                <span class="dashicons dashicons-art"></span>
                                <?php esc_html_e('Button Colors', 'evtcal-add-to-calendar'); ?>
                            </h2>
                            <p><?php esc_html_e('Customize the appearance of your calendar button:', 'evtcal-add-to-calendar'); ?></p>

                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="button_bg_color"><?php esc_html_e('Button Background Color', 'evtcal-add-to-calendar'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="button_bg_color" id="button_bg_color" value="<?php echo esc_attr($settings['button_bg_color']); ?>" class="evtcal-color-picker" />
                                        <p class="description"><?php esc_html_e('Choose the background color for the button.', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="button_hover_color"><?php esc_html_e('Button Hover Color', 'evtcal-add-to-calendar'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="button_hover_color" id="button_hover_color" value="<?php echo esc_attr($settings['button_hover_color']); ?>" class="evtcal-color-picker" />
                                        <p class="description"><?php esc_html_e('Choose the color when hovering over the button.', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="button_text_color"><?php esc_html_e('Button Text Color', 'evtcal-add-to-calendar'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="button_text_color" id="button_text_color" value="<?php echo esc_attr($settings['button_text_color']); ?>" class="evtcal-color-picker" />
                                        <p class="description"><?php esc_html_e('Choose the text color for the button.', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Preview Button -->
                            <div class="evtcal-preview-section">
                                <h3><?php esc_html_e('Button Preview', 'evtcal-add-to-calendar'); ?></h3>
                                <button type="button" class="evtcal-preview-btn" id="preview-button">
                                    <?php esc_html_e('Add to Calendar', 'evtcal-add-to-calendar'); ?> <span class="evtcal-arrow">▾</span>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Services Section -->
                        <div class="evtcal-card">
                            <h2>
                                <span class="dashicons dashicons-admin-plugins"></span>
                                <?php esc_html_e('Enable/Disable Calendar Services', 'evtcal-add-to-calendar'); ?>
                            </h2>
                            <p><?php esc_html_e('Select which calendar services to display in the dropdown menu:', 'evtcal-add-to-calendar'); ?></p>

                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Google Calendar', 'evtcal-add-to-calendar'); ?></th>
                                    <td>
                                        <label class="evtcal-toggle-switch">
                                            <input type="checkbox" name="enable_google" value="1" <?php checked($settings['enable_google'], 1); ?> />
                                            <span class="evtcal-toggle-slider"></span>
                                        </label>
                                        <p class="description"><?php esc_html_e('Enable Google Calendar integration', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Office 365', 'evtcal-add-to-calendar'); ?></th>
                                    <td>
                                        <label class="evtcal-toggle-switch">
                                            <input type="checkbox" name="enable_office365" value="1" <?php checked($settings['enable_office365'], 1); ?> />
                                            <span class="evtcal-toggle-slider"></span>
                                        </label>
                                        <p class="description"><?php esc_html_e('Enable Office 365 Calendar integration', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Outlook.com', 'evtcal-add-to-calendar'); ?></th>
                                    <td>
                                        <label class="evtcal-toggle-switch">
                                            <input type="checkbox" name="enable_outlook" value="1" <?php checked($settings['enable_outlook'], 1); ?> />
                                            <span class="evtcal-toggle-slider"></span>
                                        </label>
                                        <p class="description"><?php esc_html_e('Enable Outlook.com Calendar integration', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Yahoo Calendar', 'evtcal-add-to-calendar'); ?></th>
                                    <td>
                                        <label class="evtcal-toggle-switch">
                                            <input type="checkbox" name="enable_yahoo" value="1" <?php checked($settings['enable_yahoo'], 1); ?> />
                                            <span class="evtcal-toggle-slider"></span>
                                        </label>
                                        <p class="description"><?php esc_html_e('Enable Yahoo Calendar integration', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Apple Calendar / Outlook (.ics)', 'evtcal-add-to-calendar'); ?></th>
                                    <td>
                                        <label class="evtcal-toggle-switch">
                                            <input type="checkbox" name="enable_ics" value="1" <?php checked($settings['enable_ics'], 1); ?> />
                                            <span class="evtcal-toggle-slider"></span>
                                        </label>
                                        <p class="description"><?php esc_html_e('Enable .ics file download for Apple Calendar and Outlook Desktop', 'evtcal-add-to-calendar'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Save Button -->
                        <div class="evtcal-card evtcal-save-section">
                            <p class="submit">
                                <button type="submit" name="evtcal_save_settings" class="button button-primary button-large">
                                    <span class="dashicons dashicons-saved"></span>
                                    <?php esc_html_e('Save Settings', 'evtcal-add-to-calendar'); ?>
                                </button>
                                <button type="button" class="button button-secondary button-large evtcal-reset-btn">
                                    <span class="dashicons dashicons-image-rotate"></span>
                                    <?php esc_html_e('Reset to Defaults', 'evtcal-add-to-calendar'); ?>
                                </button>
                            </p>
                        </div>

                    </form>

                </div>

            </div>
        </div>
        <?php
    }
}
