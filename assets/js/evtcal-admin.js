/**
 * Event Add to Calendar - Admin Scripts
 * @package EvtCal_Add_To_Calendar
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeColorPickers();
        initializeTabs();
        initializeCopyButtons();
        initializeColorPreview();
        initializeResetButton();
    });

    /**
     * Initialize color pickers
     */
    function initializeColorPickers() {
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.evtcal-color-picker').wpColorPicker({
                change: function() {
                    updatePreviewButton();
                },
                clear: function() {
                    updatePreviewButton();
                }
            });
        }
    }

    /**
     * Initialize tab navigation
     */
    function initializeTabs() {
        $('.evtcal-tab-btn').on('click', function() {
            var tabId = $(this).data('tab');

            // Remove active class from all tabs and content
            $('.evtcal-tab-btn').removeClass('active');
            $('.evtcal-tab-content').removeClass('active');

            // Add active class to clicked tab and corresponding content
            $(this).addClass('active');
            $('#tab-' + tabId).addClass('active');
        });
    }

    /**
     * Initialize copy buttons
     */
    function initializeCopyButtons() {
        $('.evtcal-copy-btn').on('click', function() {
            var textToCopy = $(this).data('copy');
            var $button = $(this);
            var originalText = $button.html();

            // Create temporary textarea
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(textToCopy).select();

            try {
                // Copy to clipboard
                document.execCommand('copy');

                // Show success message
                $button.html('<span class="dashicons dashicons-yes"></span> Copied!');
                $button.css('background-color', '#22c55e');

                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.html(originalText);
                    $button.css('background-color', '');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
                $button.html('<span class="dashicons dashicons-no"></span> Failed');

                setTimeout(function() {
                    $button.html(originalText);
                }, 2000);
            }

            // Remove temporary textarea
            $temp.remove();
        });
    }

    /**
     * Initialize color preview
     */
    function initializeColorPreview() {
        // Update preview on page load
        updatePreviewButton();

        // Update preview when color changes
        $('#button_bg_color, #button_hover_color, #button_text_color').on('change', function() {
            updatePreviewButton();
        });

        // Add hover effect to preview button
        $('#preview-button').on('mouseenter', function() {
            var hoverColor = $('#button_hover_color').val() || '#333333';
            $(this).css('background-color', hoverColor);
        }).on('mouseleave', function() {
            var bgColor = $('#button_bg_color').val() || '#000000';
            $(this).css('background-color', bgColor);
        });
    }

    /**
     * Update preview button colors
     */
    function updatePreviewButton() {
        var $preview = $('#preview-button');
        var bgColor = $('#button_bg_color').val() || '#000000';
        var textColor = $('#button_text_color').val() || '#ffffff';

        $preview.css({
            'background-color': bgColor,
            'color': textColor
        });
    }

    /**
     * Initialize reset button
     */
    function initializeResetButton() {
        $('.evtcal-reset-btn').on('click', function() {
            if (confirm('Are you sure you want to reset all settings to default values?')) {
                // Reset color pickers to defaults
                $('#button_bg_color').wpColorPicker('color', '#000000');
                $('#button_hover_color').wpColorPicker('color', '#333333');
                $('#button_text_color').wpColorPicker('color', '#ffffff');

                // Enable all calendar services
                $('input[name="enable_google"]').prop('checked', true);
                $('input[name="enable_office365"]').prop('checked', true);
                $('input[name="enable_outlook"]').prop('checked', true);
                $('input[name="enable_yahoo"]').prop('checked', true);
                $('input[name="enable_ics"]').prop('checked', true);

                // Update preview
                updatePreviewButton();

                // Show notification
                showNotification('Settings have been reset to defaults. Click "Save Settings" to apply changes.', 'info');
            }
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type) {
        type = type || 'success';
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');

        $('.evtcal-admin-wrap h1').after($notice);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);

        // Make dismissible
        $(document).on('click', '.notice-dismiss', function() {
            $(this).parent().fadeOut(function() {
                $(this).remove();
            });
        });
    }

    /**
     * Form validation before submit
     */
    $('form').on('submit', function(e) {
        var bgColor = $('#button_bg_color').val();
        var hoverColor = $('#button_hover_color').val();
        var textColor = $('#button_text_color').val();

        // Validate hex colors
        var hexPattern = /^#[0-9A-F]{6}$/i;

        if (!hexPattern.test(bgColor) || !hexPattern.test(hoverColor) || !hexPattern.test(textColor)) {
            e.preventDefault();
            alert('Please enter valid hex color codes (e.g., #000000)');
            return false;
        }

        return true;
    });

})(jQuery);
