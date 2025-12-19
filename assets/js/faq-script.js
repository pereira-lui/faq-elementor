/**
 * FAQ Elementor - JavaScript do Frontend
 * Busca em tempo real e tracking de tags populares
 */

(function($) {
    'use strict';

    // Debounce function for search
    function debounce(func, wait) {
        var timeout;
        return function executedFunction() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Initialize FAQ functionality
    function initFAQ() {
        
        // Store original FAQ items IDs for each container
        $('.faq-container').each(function() {
            var $container = $(this);
            var originalIds = [];
            $container.find('.faq-item').each(function() {
                originalIds.push($(this).data('id').toString());
            });
            $container.data('original-ids', originalIds);
        });
        
        // Accordion toggle
        $(document).on('click', '.faq-question-wrapper', function(e) {
            e.preventDefault();
            
            var $item = $(this).closest('.faq-item');
            
            // Toggle current item
            $item.toggleClass('active');
        });

        // Real-time search
        $(document).on('input', '.faq-search-input', debounce(function(e) {
            var $input = $(this);
            var $container = $input.closest('.faq-container');
            var searchTerm = $input.val().trim();
            
            // Clear tag selection when searching
            $container.find('.faq-tag-btn').removeClass('active');
            
            // Show/hide clear button
            updateClearButtons($container);
            
            if (searchTerm.length < 2) {
                // If search is too short, restore original items
                restoreOriginalItems($container);
                return;
            }
            
            // Perform AJAX search
            performSearch($container, searchTerm, '');
            
        }, 300));

        // Clear search button click
        $(document).on('click', '.faq-search-clear', function(e) {
            e.preventDefault();
            var $container = $(this).closest('.faq-container');
            var $input = $container.find('.faq-search-input');
            
            $input.val('');
            restoreOriginalItems($container);
            updateClearButtons($container);
            $input.focus();
        });

        // Clear all button click (search + tags)
        $(document).on('click', '.faq-clear-all-btn', function(e) {
            e.preventDefault();
            var $container = $(this).closest('.faq-container');
            
            // Clear search
            $container.find('.faq-search-input').val('');
            
            // Clear tag selection
            $container.find('.faq-tag-btn').removeClass('active');
            
            // Restore original items
            restoreOriginalItems($container);
            
            updateClearButtons($container);
        });

        // Tag filter functionality with tracking
        $(document).on('click', '.faq-tag-btn', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var $container = $btn.closest('.faq-container');
            var tag = $btn.data('tag');
            
            // Clear search field
            $container.find('.faq-search-input').val('');
            
            // Toggle active state
            if ($btn.hasClass('active')) {
                // Deselect - restore original items
                $btn.removeClass('active');
                restoreOriginalItems($container);
            } else {
                // Select this tag
                $container.find('.faq-tag-btn').removeClass('active');
                $btn.addClass('active');
                
                // Track tag click
                trackTagClick(tag);
                
                // Perform search by tag
                performSearch($container, '', tag);
            }
            
            updateClearButtons($container);
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
        
        // Clear search on Escape key
        $(document).on('keydown', '.faq-search-input', function(e) {
            if (e.key === 'Escape') {
                var $container = $(this).closest('.faq-container');
                $(this).val('');
                $container.find('.faq-tag-btn').removeClass('active');
                restoreOriginalItems($container);
                updateClearButtons($container);
            }
        });
    }

    /**
     * Restore original FAQ items (remove dynamically added ones)
     */
    function restoreOriginalItems($container) {
        var originalIds = $container.data('original-ids') || [];
        var $items = $container.find('.faq-items');
        var $noResults = $container.find('.faq-no-results');
        
        // Remove highlight from all items
        $items.find('.faq-question').each(function() {
            var $question = $(this);
            // Remove highlight spans and restore plain text
            $question.html($question.text());
        });
        
        // Show only original items, remove dynamically added ones
        $items.find('.faq-item').each(function() {
            var $item = $(this);
            var itemId = $item.data('id').toString();
            
            if (originalIds.indexOf(itemId) !== -1) {
                // Original item - show it
                $item.removeClass('hidden active').show();
            } else {
                // Dynamically added item - remove it
                $item.remove();
            }
        });
        
        $noResults.hide();
    }

    /**
     * Update visibility of clear buttons
     */
    function updateClearButtons($container) {
        var searchTerm = $container.find('.faq-search-input').val().trim();
        var hasActiveTag = $container.find('.faq-tag-btn.active').length > 0;
        
        // Show/hide search clear button
        if (searchTerm.length > 0) {
            $container.find('.faq-search-clear').show();
        } else {
            $container.find('.faq-search-clear').hide();
        }
        
        // Show/hide clear all button
        if (searchTerm.length > 0 || hasActiveTag) {
            $container.find('.faq-clear-all-btn').show();
        } else {
            $container.find('.faq-clear-all-btn').hide();
        }
    }

    /**
     * Perform AJAX search
     */
    function performSearch($container, searchTerm, tag) {
        var $wrapper = $container.find('.faq-items-wrapper');
        var $items = $container.find('.faq-items');
        var $noResults = $container.find('.faq-no-results');
        var $searchIcon = $container.find('.faq-search-icon');
        var $searchLoading = $container.find('.faq-search-loading');
        
        // Show loading state
        $wrapper.addClass('loading');
        $searchIcon.hide();
        $searchLoading.show();
        
        $.ajax({
            url: faqElementor.ajaxUrl,
            type: 'POST',
            data: {
                action: 'faq_search',
                nonce: faqElementor.nonce,
                search: searchTerm,
                tag: tag
            },
            success: function(response) {
                $wrapper.removeClass('loading');
                $searchIcon.show();
                $searchLoading.hide();
                
                if (response.success && response.data) {
                    renderSearchResults($container, response.data, searchTerm);
                } else {
                    // Show no results
                    $items.find('.faq-item').addClass('hidden').hide();
                    $noResults.show();
                }
            },
            error: function() {
                $wrapper.removeClass('loading');
                $searchIcon.show();
                $searchLoading.hide();
                
                // Fallback to client-side filtering
                filterItemsLocally($container, searchTerm, tag);
            }
        });
    }

    /**
     * Render search results
     */
    function renderSearchResults($container, results, searchTerm) {
        var $items = $container.find('.faq-items');
        var $noResults = $container.find('.faq-no-results');
        
        if (results.length === 0) {
            $items.find('.faq-item').addClass('hidden').hide();
            $noResults.show();
            return;
        }
        
        $noResults.hide();
        
        // Get array of result IDs
        var resultIds = results.map(function(item) {
            return item.id.toString();
        });
        
        // Hide all items first
        $items.find('.faq-item').each(function() {
            var $item = $(this);
            var itemId = $item.data('id').toString();
            
            if (resultIds.indexOf(itemId) !== -1) {
                $item.removeClass('hidden').show();
                
                // Highlight search term in question
                if (searchTerm) {
                    highlightText($item.find('.faq-question'), searchTerm);
                }
            } else {
                $item.addClass('hidden').hide();
            }
        });
        
        // If we have results from server that aren't in DOM, add them
        results.forEach(function(result) {
            var existingItem = $items.find('.faq-item[data-id="' + result.id + '"]');
            
            if (existingItem.length === 0) {
                // Create new FAQ item
                var newItem = createFaqItem(result);
                $items.append(newItem);
                
                if (searchTerm) {
                    highlightText($(newItem).find('.faq-question'), searchTerm);
                }
            }
        });
    }

    /**
     * Create FAQ item HTML
     */
    function createFaqItem(data) {
        var tagsAttr = data.tags ? data.tags.join(',') : '';
        
        var html = '<div class="faq-item" data-id="' + data.id + '" data-tags="' + tagsAttr + '">' +
            '<div class="faq-question-wrapper" tabindex="0" role="button">' +
                '<span class="faq-question">' + escapeHtml(data.title) + '</span>' +
                '<span class="faq-icon">' +
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                        '<line x1="12" y1="5" x2="12" y2="19" class="faq-icon-vertical"></line>' +
                        '<line x1="5" y1="12" x2="19" y2="12"></line>' +
                    '</svg>' +
                '</span>' +
            '</div>' +
            '<div class="faq-answer">' + data.content + '</div>' +
        '</div>';
        
        return html;
    }

    /**
     * Escape HTML for safe insertion
     */
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Highlight search term in text
     */
    function highlightText($element, searchTerm) {
        var text = $element.text();
        var regex = new RegExp('(' + escapeRegExp(searchTerm) + ')', 'gi');
        var highlighted = text.replace(regex, '<span class="faq-highlight">$1</span>');
        $element.html(highlighted);
    }

    /**
     * Escape special regex characters
     */
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Fallback: Filter items locally (client-side)
     */
    function filterItemsLocally($container, searchTerm, tag) {
        var $items = $container.find('.faq-item');
        var $noResults = $container.find('.faq-no-results');
        var hasResults = false;
        
        $items.each(function() {
            var $item = $(this);
            var show = true;
            
            // Filter by search term
            if (searchTerm) {
                var question = $item.find('.faq-question').text().toLowerCase();
                var answer = $item.find('.faq-answer').text().toLowerCase();
                var search = searchTerm.toLowerCase();
                
                if (question.indexOf(search) === -1 && answer.indexOf(search) === -1) {
                    show = false;
                }
            }
            
            // Filter by tag
            if (tag && show) {
                var itemTags = $item.data('tags');
                if (itemTags) {
                    var tagsArray = itemTags.toString().split(',');
                    if (tagsArray.indexOf(tag) === -1) {
                        show = false;
                    }
                } else {
                    show = false;
                }
            }
            
            if (show) {
                $item.removeClass('hidden').show();
                hasResults = true;
                
                if (searchTerm) {
                    highlightText($item.find('.faq-question'), searchTerm);
                }
            } else {
                $item.addClass('hidden').hide();
            }
        });
        
        if (hasResults) {
            $noResults.hide();
        } else {
            $noResults.show();
        }
    }

    /**
     * Track tag click for popularity
     */
    function trackTagClick(tag) {
        if (!faqElementor || !faqElementor.ajaxUrl) {
            return;
        }
        
        $.ajax({
            url: faqElementor.ajaxUrl,
            type: 'POST',
            data: {
                action: 'faq_track_tag',
                nonce: faqElementor.nonce,
                tag: tag
            },
            success: function(response) {
                // Tag tracked successfully
                // Optionally refresh tag order
            }
        });
    }

    /**
     * Refresh popular tags order
     */
    function refreshPopularTags($container) {
        if (!faqElementor || !faqElementor.ajaxUrl) {
            return;
        }
        
        var maxTags = $container.find('.faq-tags-filter').data('max-tags') || 6;
        
        $.ajax({
            url: faqElementor.ajaxUrl,
            type: 'POST',
            data: {
                action: 'faq_get_popular_tags',
                nonce: faqElementor.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    updateTagsOrder($container, response.data.slice(0, maxTags));
                }
            }
        });
    }

    /**
     * Update tags order in DOM
     */
    function updateTagsOrder($container, tags) {
        var $tagsContainer = $container.find('.faq-tags-filter');
        var activeTag = $tagsContainer.find('.faq-tag-btn.active').data('tag');
        
        $tagsContainer.empty();
        
        tags.forEach(function(tag) {
            var activeClass = (tag.slug === activeTag) ? ' active' : '';
            var $btn = $('<button type="button" class="faq-tag-btn' + activeClass + '" data-tag="' + tag.slug + '">' + 
                tag.name.toUpperCase() + '</button>');
            $tagsContainer.append($btn);
        });
    }

    // Initialize on document ready
    $(document).ready(function() {
        initFAQ();
    });

    // Re-initialize on Elementor frontend init (for live preview)
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/faq_widget.default', function($scope) {
                initFAQ();
            });
        }
    });

})(jQuery);
