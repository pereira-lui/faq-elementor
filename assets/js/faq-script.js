/**
 * FAQ Elementor - JavaScript do Frontend
 */

(function($) {
    'use strict';

    // Initialize FAQ functionality
    function initFAQ() {
        // Accordion toggle
        $(document).on('click', '.faq-question-wrapper', function(e) {
            e.preventDefault();
            
            var $item = $(this).closest('.faq-item');
            var $container = $(this).closest('.faq-container');
            
            // Close other items (optional - remove if you want multiple open)
            // $container.find('.faq-item').not($item).removeClass('active');
            
            // Toggle current item
            $item.toggleClass('active');
        });

        // Tag filter functionality
        $(document).on('click', '.faq-tag-btn', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var $container = $btn.closest('.faq-container');
            var tag = $btn.data('tag');
            
            // Toggle active state
            if ($btn.hasClass('active')) {
                // Deselect - show all items
                $btn.removeClass('active');
                $container.find('.faq-item').removeClass('hidden').show();
            } else {
                // Select this tag
                $container.find('.faq-tag-btn').removeClass('active');
                $btn.addClass('active');
                
                // Filter items
                $container.find('.faq-item').each(function() {
                    var $item = $(this);
                    var itemTags = $item.data('tags');
                    
                    if (itemTags) {
                        var tagsArray = itemTags.toString().split(',');
                        
                        if (tagsArray.indexOf(tag) !== -1) {
                            $item.removeClass('hidden').fadeIn(300);
                        } else {
                            $item.addClass('hidden').fadeOut(200);
                        }
                    } else {
                        $item.addClass('hidden').fadeOut(200);
                    }
                });
            }
        });

        // Keyboard accessibility
        $(document).on('keydown', '.faq-question-wrapper', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });

        // Make question wrapper focusable
        $('.faq-question-wrapper').attr('tabindex', '0').attr('role', 'button');
    }

    // Initialize on document ready
    $(document).ready(function() {
        initFAQ();
    });

    // Re-initialize on Elementor frontend init (for live preview)
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/faq_widget.default', function($scope) {
                // Widget-specific initialization if needed
                initFAQ();
            });
        }
    });

})(jQuery);
