/**
 * FAQ Elementor Page Widget - JavaScript
 */

(function($) {
    'use strict';

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize Sticky Categories
    function initStickyCategories($container) {
        // Check if sticky is enabled
        const stickyEnabled = $container.data('sticky-categories');
        if (stickyEnabled !== 'yes') return;
        
        const $categoriesTabs = $container.find('.faq-page-categories-tabs');
        
        if (!$categoriesTabs.length) return;
        
        // Create placeholder element to maintain layout when sticky
        const $placeholder = $('<div class="faq-page-categories-placeholder"></div>');
        $categoriesTabs.after($placeholder);
        
        // Get background color from container or parent
        let bgColor = $container.find('.faq-page-main-content').css('background-color');
        if (bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
            bgColor = $container.css('background-color');
        }
        if (bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
            bgColor = $container.closest('section, .elementor-section, .elementor-widget-container').css('background-color');
        }
        if (bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
            bgColor = '#1a1a2e'; // Fallback color
        }
        
        let originalTop = $categoriesTabs.offset().top;
        let originalHeight = $categoriesTabs.outerHeight();
        let isSticky = false;
        
        $(window).on('scroll.faqPageSticky', function() {
            const scrollTop = $(window).scrollTop();
            
            // Check if we should make it sticky - just lock at top, no animation
            if (scrollTop >= originalTop && !isSticky) {
                isSticky = true;
                $categoriesTabs.addClass('is-sticky');
                $categoriesTabs.css('background-color', bgColor);
                $placeholder.css('height', originalHeight + 'px');
                $placeholder.addClass('is-visible');
            } else if (scrollTop < originalTop && isSticky) {
                isSticky = false;
                $categoriesTabs.removeClass('is-sticky');
                $categoriesTabs.css('background-color', '');
                $placeholder.removeClass('is-visible');
            }
        });
        
        // Recalculate on resize
        $(window).on('resize.faqPageSticky', debounce(function() {
            if (!isSticky) {
                originalTop = $categoriesTabs.offset().top;
                originalHeight = $categoriesTabs.outerHeight();
            }
        }, 100));
    }

    // Initialize Category Slider with Swiper
    function initCategorySlider($container) {
        const $slider = $container.find('.faq-page-category-slider');
        const $swiperEl = $slider.find('.faq-page-swiper');
        
        if (!$swiperEl.length) return;
        
        // Check if Swiper is available
        if (typeof Swiper === 'undefined') {
            console.warn('Swiper not loaded');
            return;
        }

        const $nextBtn = $slider.find('.faq-page-swiper-next');
        const $categoriesTabs = $container.find('.faq-page-categories-tabs');

        // Initialize Swiper - começando do início (esquerda)
        const swiper = new Swiper($swiperEl[0], {
            slidesPerView: 'auto',
            spaceBetween: 10,
            freeMode: true,
            grabCursor: true,
            watchOverflow: true,
            initialSlide: 0, // Começa no primeiro slide (esquerda)
            navigation: {
                nextEl: $nextBtn[0],
            },
            // Configurações de touch
            touchRatio: 1,
            touchAngle: 45,
            simulateTouch: true,
            shortSwipes: true,
            longSwipesRatio: 0.5,
            // Breakpoints para responsividade
            breakpoints: {
                320: {
                    spaceBetween: 8,
                },
                768: {
                    spaceBetween: 10,
                },
                1024: {
                    spaceBetween: 12,
                }
            }
        });

        // Store swiper instance on container for later access
        $container.data('category-swiper', swiper);

        // Update swiper on window resize
        $(window).on('resize', debounce(function() {
            if (swiper && swiper.update) {
                swiper.update();
            }
        }, 100));
    }

    // Initialize FAQ Page Widget
    function initFAQPageWidget($container) {
        if (!$container.length || $container.data('faq-page-initialized')) {
            return;
        }

        $container.data('faq-page-initialized', true);

        // Initialize category slider (Swiper)
        initCategorySlider($container);

        // Initialize sticky categories
        initStickyCategories($container);

        const widgetId = $container.data('widget-id');
        const postsPerPage = parseInt($container.data('posts-per-page')) || 10;
        const paginationType = $container.data('pagination-type') || 'load_more';
        const allowMultiple = $container.data('allow-multiple') === 'yes';
        let maxPages = parseInt($container.data('max-pages')) || 1;

        let currentPage = 1;
        let currentCategory = 'all';
        let currentSearch = '';
        let isLoading = false;

        const $searchInput = $container.find('.faq-page-search-input');
        const $searchClear = $container.find('.faq-page-search-clear');
        const $searchIcon = $container.find('.faq-page-search-icon');
        const $searchLoading = $container.find('.faq-page-search-loading');
        const $categoryBtns = $container.find('.faq-page-category-btn');
        const $itemsWrapper = $container.find('.faq-page-items-wrapper');
        const $itemsContainer = $container.find('.faq-page-items');
        const $noResults = $container.find('.faq-page-search-no-results');
        const $loadMoreBtn = $container.find('.faq-page-load-more-btn');
        const $paginationNums = $container.find('.faq-page-pagination-num');

        // Esconder botão "carregar mais" se só tem uma página no carregamento inicial
        if ($loadMoreBtn.length && maxPages <= 1) {
            $loadMoreBtn.hide();
        }

        // Toggle FAQ item
        function toggleItem($item) {
            const isActive = $item.hasClass('active');

            if (!allowMultiple) {
                // Close all other items
                $itemsContainer.find('.faq-page-item.active').not($item).removeClass('active');
            }

            $item.toggleClass('active', !isActive);
        }

        // FAQ item click handler
        $container.on('click', '.faq-page-question-wrapper', function() {
            const $item = $(this).closest('.faq-page-item');
            toggleItem($item);
        });

        // Keyboard accessibility
        $container.on('keydown', '.faq-page-question-wrapper', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const $item = $(this).closest('.faq-page-item');
                toggleItem($item);
            }
        });

        // Search functionality - busca em tempo real
        const performSearch = debounce(function(query) {
            currentSearch = query.trim();
            currentPage = 1;

            if (currentSearch.length === 0) {
                $searchClear.hide();
                removeHighlights();
                resetToInitial();
                return;
            }

            $searchClear.show();
            
            // Primeiro faz filtro local para resposta imediata
            filterAndHighlightLocal(currentSearch);
            
            // Depois busca no servidor para resultados completos
            loadFAQs(true);
        }, 150);

        // Filtro e highlight local para resposta imediata
        function filterAndHighlightLocal(search) {
            if (!search || search.length < 2) return;
            
            const searchLower = search.toLowerCase();
            const terms = search.split(/\s+/).filter(t => t.length > 1);
            
            $itemsContainer.find('.faq-page-item').each(function() {
                const $item = $(this);
                const questionText = $item.find('.faq-page-question').text().toLowerCase();
                const answerText = $item.find('.faq-page-answer-content').text().toLowerCase();
                
                // Verifica se algum termo aparece na pergunta ou resposta
                const matches = terms.some(term => 
                    questionText.includes(term.toLowerCase()) || 
                    answerText.includes(term.toLowerCase())
                );
                
                if (matches) {
                    $item.removeClass('hidden').show();
                } else {
                    $item.addClass('hidden').hide();
                }
            });
            
            // Aplica highlight
            highlightSearchTerms(search);
            
            // Verifica se há resultados visíveis
            const visibleItems = $itemsContainer.find('.faq-page-item:not(.hidden)').length;
            if (visibleItems === 0) {
                $noResults.show();
            } else {
                $noResults.hide();
            }
        }
        
        // Remove highlights
        function removeHighlights() {
            $itemsContainer.find('.faq-page-highlight').each(function() {
                const $highlight = $(this);
                $highlight.replaceWith($highlight.text());
            });
        }

        $searchInput.on('input', function() {
            const query = $(this).val();
            performSearch(query);
        });

        // Clear search
        $searchClear.on('click', function() {
            $searchInput.val('');
            currentSearch = '';
            $searchClear.hide();
            removeHighlights();
            resetToInitial();
        });

        // Category filter
        $categoryBtns.on('click', function() {
            const $btn = $(this);
            const category = $btn.data('category');

            // Update active state
            $categoryBtns.removeClass('active');
            $btn.addClass('active');

            // Set category and reload
            currentCategory = category;
            currentPage = 1;
            
            // Remove highlights when changing category
            removeHighlights();
            // Clear search when changing category
            if (currentSearch) {
                $searchInput.val('');
                currentSearch = '';
                $searchClear.hide();
            }

            loadFAQs(true);

            // Track tag click
            if (category !== 'all' && typeof faqElementor !== 'undefined') {
                $.post(faqElementor.ajaxUrl, {
                    action: 'faq_track_tag',
                    nonce: faqElementor.nonce,
                    tag: category
                });
            }
        });

        // Load more button
        $loadMoreBtn.on('click', function() {
            if (isLoading) return;
            if (currentPage >= maxPages) {
                $loadMoreBtn.hide();
                return;
            }
            currentPage++;
            loadFAQs(false);
        });

        // Pagination numbers
        $paginationNums.on('click', function() {
            const page = parseInt($(this).data('page'));
            if (isLoading || page === currentPage) return;

            $paginationNums.removeClass('active');
            $(this).addClass('active');

            currentPage = page;
            loadFAQs(true);

            // Scroll to top of FAQ items
            $('html, body').animate({
                scrollTop: $itemsWrapper.offset().top - 100
            }, 300);
        });

        // Infinite scroll
        if (paginationType === 'infinite') {
            let scrollTimeout;
            $(window).on('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    if (isLoading || currentPage >= maxPages) return;

                    const scrollPos = $(window).scrollTop() + $(window).height();
                    const triggerPos = $itemsWrapper.offset().top + $itemsWrapper.height() - 200;

                    if (scrollPos >= triggerPos) {
                        currentPage++;
                        loadFAQs(false);
                    }
                }, 100);
            });
        }

        // Load FAQs via AJAX
        function loadFAQs(replace) {
            if (isLoading) return;
            isLoading = true;

            // Show loading state
            $itemsWrapper.addClass('loading');
            $searchIcon.hide();
            $searchLoading.show();
            
            if ($loadMoreBtn.length) {
                $loadMoreBtn.prop('disabled', true);
                $loadMoreBtn.find('.faq-page-load-more-text').hide();
                $loadMoreBtn.find('.faq-page-load-more-loading').show();
                $loadMoreBtn.find('.faq-page-load-more-spinner').show();
            }

            const data = {
                action: 'faq_page_search',
                nonce: faqElementor.nonce,
                search: currentSearch,
                category: currentCategory,
                page: currentPage,
                posts_per_page: postsPerPage
            };

            $.post(faqElementor.ajaxUrl, data, function(response) {
                isLoading = false;
                $itemsWrapper.removeClass('loading');
                $searchIcon.show();
                $searchLoading.hide();

                if ($loadMoreBtn.length) {
                    $loadMoreBtn.prop('disabled', false);
                    $loadMoreBtn.find('.faq-page-load-more-text').show();
                    $loadMoreBtn.find('.faq-page-load-more-loading').hide();
                    $loadMoreBtn.find('.faq-page-load-more-spinner').hide();
                }

                if (response.success) {
                    const items = response.data.items;
                    const hasMore = response.data.has_more;
                    const maxPagesNew = response.data.max_pages;
                    
                    // Atualiza maxPages global
                    maxPages = maxPagesNew;

                    if (replace) {
                        $itemsContainer.empty();
                    }

                    if (items.length === 0 && replace) {
                        $itemsContainer.hide();
                        $noResults.show();
                        // Esconde botão se não há resultados
                        if ($loadMoreBtn.length) {
                            $loadMoreBtn.hide();
                        }
                    } else {
                        $noResults.hide();
                        $itemsContainer.show();

                        items.forEach(function(item) {
                            const $newItem = createFAQItem(item);
                            $itemsContainer.append($newItem);
                        });

                        // Highlight search terms
                        if (currentSearch) {
                            highlightSearchTerms(currentSearch);
                        }
                        
                        // Update load more button visibility
                        if ($loadMoreBtn.length) {
                            // Esconde se não há mais páginas OU se chegamos na última página
                            if (!hasMore || currentPage >= maxPages) {
                                $loadMoreBtn.hide();
                            } else {
                                $loadMoreBtn.show();
                            }
                        }
                    }

                    // Update pagination numbers
                    updatePaginationNumbers(maxPagesNew);
                }
            }).fail(function() {
                isLoading = false;
                $itemsWrapper.removeClass('loading');
                $searchIcon.show();
                $searchLoading.hide();

                if ($loadMoreBtn.length) {
                    $loadMoreBtn.prop('disabled', false);
                    $loadMoreBtn.find('.faq-page-load-more-text').show();
                    $loadMoreBtn.find('.faq-page-load-more-loading').hide();
                    $loadMoreBtn.find('.faq-page-load-more-spinner').hide();
                }
            });
        }

        // Create FAQ item HTML
        function createFAQItem(item) {
            const tagsAttr = item.tags ? item.tags.join(',') : '';
            const $item = $(`
                <div class="faq-page-item" data-id="${item.id}" data-tags="${tagsAttr}">
                    <div class="faq-page-question-wrapper" tabindex="0" role="button" aria-expanded="false">
                        <span class="faq-page-question">${escapeHtml(item.title)}</span>
                        <span class="faq-page-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    </div>
                    <div class="faq-page-answer">
                        <div class="faq-page-answer-content">
                            ${item.content}
                        </div>
                    </div>
                </div>
            `);
            return $item;
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Highlight search terms
        function highlightSearchTerms(search) {
            const terms = search.split(/\s+/).filter(t => t.length > 2);
            if (terms.length === 0) return;

            const regex = new RegExp('(' + terms.map(t => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|') + ')', 'gi');

            $itemsContainer.find('.faq-page-question, .faq-page-answer-content').each(function() {
                const $el = $(this);
                const html = $el.html();
                
                // Only highlight text nodes, not HTML tags
                const highlighted = html.replace(/>([^<]+)</g, function(match, text) {
                    return '>' + text.replace(regex, '<span class="faq-page-highlight">$1</span>') + '<';
                });
                
                $el.html(highlighted);
            });
        }

        // Update pagination numbers
        function updatePaginationNumbers(newMaxPages) {
            const $paginationContainer = $container.find('.faq-page-pagination-numbers');
            if (!$paginationContainer.length) return;

            $paginationContainer.empty();

            for (let i = 1; i <= newMaxPages; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                $paginationContainer.append(`
                    <button type="button" class="faq-page-pagination-num ${activeClass}" data-page="${i}">${i}</button>
                `);
            }

            // Re-bind click events
            $paginationContainer.find('.faq-page-pagination-num').on('click', function() {
                const page = parseInt($(this).data('page'));
                if (isLoading || page === currentPage) return;

                $paginationContainer.find('.faq-page-pagination-num').removeClass('active');
                $(this).addClass('active');

                currentPage = page;
                loadFAQs(true);

                $('html, body').animate({
                    scrollTop: $itemsWrapper.offset().top - 100
                }, 300);
            });
        }

        // Reset to initial state
        function resetToInitial() {
            currentCategory = 'all';
            currentPage = 1;

            // Reset category buttons
            $categoryBtns.removeClass('active');
            $categoryBtns.filter('[data-category="all"]').addClass('active');
            
            // Remove highlights
            removeHighlights();
            
            // Show all items
            $itemsContainer.find('.faq-page-item').removeClass('hidden').show();
            $noResults.hide();

            loadFAQs(true);
        }

        // Make question wrappers focusable
        $container.find('.faq-page-question-wrapper').attr({
            'tabindex': '0',
            'role': 'button',
            'aria-expanded': 'false'
        });

        // Update aria-expanded on toggle
        $container.on('click', '.faq-page-question-wrapper', function() {
            const $item = $(this).closest('.faq-page-item');
            const isExpanded = $item.hasClass('active');
            $(this).attr('aria-expanded', isExpanded);
        });
    }

    // Initialize on document ready
    $(document).ready(function() {
        $('.faq-page-container').each(function() {
            initFAQPageWidget($(this));
        });
    });

    // Initialize for Elementor frontend
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/faq_pda_page_widget.default', function($scope) {
                initFAQPageWidget($scope.find('.faq-page-container'));
            });
        }
    });

})(jQuery);
