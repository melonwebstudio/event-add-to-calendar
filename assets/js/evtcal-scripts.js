/**
 * Event Add to Calendar - Frontend Scripts
 * @package EvtCal_Add_To_Calendar
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        initializeDropdowns();
    }

    /**
     * Initialize all calendar dropdowns
     */
    function initializeDropdowns() {
        const containers = document.querySelectorAll('.evtcal');

        containers.forEach(function(container) {
            const button = container.querySelector('.evtcal-btn');
            const dropdown = container.querySelector('.evtcal-dropdown');

            if (!button || !dropdown) {
                return;
            }

            // Toggle dropdown on button click
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown(container);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    closeDropdown(container);
                }
            });

            // Handle keyboard navigation
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleDropdown(container);
                } else if (e.key === 'Escape') {
                    closeDropdown(container);
                }
            });

            // Close dropdown when pressing Escape
            dropdown.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDropdown(container);
                    button.focus();
                }
            });

            // Handle arrow key navigation within dropdown
            const links = dropdown.querySelectorAll('a');
            links.forEach(function(link, index) {
                link.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const nextLink = links[index + 1];
                        if (nextLink) {
                            nextLink.focus();
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const prevLink = links[index - 1];
                        if (prevLink) {
                            prevLink.focus();
                        } else {
                            button.focus();
                        }
                    }
                });
            });
        });
    }

    /**
     * Toggle dropdown visibility
     * @param {HTMLElement} container
     */
    function toggleDropdown(container) {
        const isActive = container.classList.contains('active');

        // Close all other dropdowns first
        closeAllDropdowns();

        if (!isActive) {
            openDropdown(container);
        }
    }

    /**
     * Open a specific dropdown
     * @param {HTMLElement} container
     */
    function openDropdown(container) {
        const button = container.querySelector('.evtcal-btn');

        container.classList.add('active');
        button.setAttribute('aria-expanded', 'true');

        // Focus first link after a short delay
        setTimeout(function() {
            const firstLink = container.querySelector('.evtcal-dropdown a');
            if (firstLink) {
                firstLink.focus();
            }
        }, 100);
    }

    /**
     * Close a specific dropdown
     * @param {HTMLElement} container
     */
    function closeDropdown(container) {
        const button = container.querySelector('.evtcal-btn');

        container.classList.remove('active');
        button.setAttribute('aria-expanded', 'false');
    }

    /**
     * Close all dropdowns
     */
    function closeAllDropdowns() {
        const activeContainers = document.querySelectorAll('.evtcal.active');
        activeContainers.forEach(function(container) {
            closeDropdown(container);
        });
    }

})();
