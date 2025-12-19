<?php
/**
 * FAQ Elementor Widget
 *
 * @package FAQ_Elementor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * FAQ Widget Class
 */
class FAQ_Elementor_Widget extends \Elementor\Widget_Base {

    /**
     * Constructor
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
    }

    /**
     * Get widget name
     */
    public function get_name() {
        return 'faq_pda_widget';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('FAQ PDA', 'faq-elementor');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-help-o';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['faq-pda', 'general'];
    }

    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['faq', 'perguntas', 'frequentes', 'accordion', 'questions', 'answers', 'busca', 'search'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['faq-elementor-style'];
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['faq-elementor-script'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {

        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Conteúdo', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Título', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Perguntas Frequentes', 'faq-elementor'),
                'placeholder' => __('Digite o título', 'faq-elementor'),
            ]
        );

        $this->add_control(
            'show_search',
            [
                'label' => __('Mostrar Campo de Busca', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'search_placeholder',
            [
                'label' => __('Placeholder da Busca', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Digite sua pergunta...', 'faq-elementor'),
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_tags_filter',
            [
                'label' => __('Mostrar Tags Populares', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Tags são ordenadas automaticamente pelas mais buscadas.', 'faq-elementor'),
            ]
        );

        $this->add_control(
            'max_tags',
            [
                'label' => __('Máximo de Tags', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 10,
                'condition' => [
                    'show_tags_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Número Inicial de Perguntas', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 100,
                'step' => 1,
                'default' => 7,
                'description' => __('-1 para mostrar todas. Na busca, mostra todos os resultados.', 'faq-elementor'),
            ]
        );

        $this->end_controls_section();

        // Style Section - Layout
        $this->start_controls_section(
            'section_layout_style',
            [
                'label' => __('Layout', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'layout_style',
            [
                'label' => __('Estilo do Layout', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'two-columns',
                'options' => [
                    'one-column' => __('Uma Coluna', 'faq-elementor'),
                    'two-columns' => __('Duas Colunas', 'faq-elementor'),
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .faq-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Search
        $this->start_controls_section(
            'section_search_style',
            [
                'label' => __('Campo de Busca', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'search_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255,255,255,0.1)',
                'selectors' => [
                    '{{WRAPPER}} .faq-search-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_text_color',
            [
                'label' => __('Cor do Texto', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-search-input' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .faq-search-input::placeholder' => 'color: {{VALUE}}; opacity: 0.6;',
                ],
            ]
        );

        $this->add_control(
            'search_border_color',
            [
                'label' => __('Cor da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255,255,255,0.3)',
                'selectors' => [
                    '{{WRAPPER}} .faq-search-input' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_border_radius',
            [
                'label' => __('Border Radius', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-search-input' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Title
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Título', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Cor do Título', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .faq-title',
            ]
        );

        $this->end_controls_section();

        // Style Section - Tags
        $this->start_controls_section(
            'section_tags_style',
            [
                'label' => __('Tags', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tag_bg_color',
            [
                'label' => __('Cor de Fundo da Tag', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-tag-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tag_text_color',
            [
                'label' => __('Cor do Texto da Tag', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a2e',
                'selectors' => [
                    '{{WRAPPER}} .faq-tag-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tag_active_bg_color',
            [
                'label' => __('Cor de Fundo Tag Ativa', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .faq-tag-btn.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tag_border_radius',
            [
                'label' => __('Border Radius', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-tag-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - FAQ Items
        $this->start_controls_section(
            'section_items_style',
            [
                'label' => __('Itens FAQ', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_border_color',
            [
                'label' => __('Cor da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => __('Border Radius', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'question_color',
            [
                'label' => __('Cor da Pergunta', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-question' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'answer_color',
            [
                'label' => __('Cor da Resposta', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
                'selectors' => [
                    '{{WRAPPER}} .faq-answer' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Cor do Ícone', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'question_typography',
                'label' => __('Tipografia da Pergunta', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-question',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'answer_typography',
                'label' => __('Tipografia da Resposta', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-answer',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Query arguments for initial load
        $args = [
            'post_type' => 'faq_item',
            'posts_per_page' => $settings['posts_per_page'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $faq_query = new WP_Query($args);

        // Get popular tags (sorted by usage)
        $popular_tags = FAQ_Elementor::get_popular_tags_list();
        $max_tags = isset($settings['max_tags']) ? intval($settings['max_tags']) : 10;
        $popular_tags = array_slice($popular_tags, 0, $max_tags);

        $layout_class = $settings['layout_style'] === 'two-columns' ? 'faq-two-columns' : 'faq-one-column';
        $widget_id = $this->get_id();
        ?>

        <div class="faq-container <?php echo esc_attr($layout_class); ?>" data-widget-id="<?php echo esc_attr($widget_id); ?>">
            <div class="faq-header">
                <?php if (!empty($settings['title'])) : ?>
                    <h2 class="faq-title"><?php echo esc_html($settings['title']); ?></h2>
                <?php endif; ?>

                <?php if ($settings['show_search'] === 'yes') : ?>
                    <div class="faq-search-wrapper">
                        <input 
                            type="text" 
                            class="faq-search-input" 
                            placeholder="<?php echo esc_attr($settings['search_placeholder']); ?>"
                            autocomplete="off"
                        >
                        <button type="button" class="faq-search-clear" style="display: none;" title="<?php esc_attr_e('Limpar busca', 'faq-elementor'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        <span class="faq-search-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="M21 21l-4.35-4.35"></path>
                            </svg>
                        </span>
                        <span class="faq-search-loading" style="display: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32">
                                    <animate attributeName="stroke-dashoffset" dur="1s" values="32;0" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($settings['show_tags_filter'] === 'yes' && !empty($popular_tags)) : ?>
                    <div class="faq-tags-filter" data-max-tags="<?php echo esc_attr($max_tags); ?>">
                        <?php foreach ($popular_tags as $tag) : ?>
                            <button type="button" class="faq-tag-btn" data-tag="<?php echo esc_attr($tag['slug']); ?>">
                                <?php echo esc_html(strtoupper($tag['name'])); ?>
                            </button>
                        <?php endforeach; ?>
                        <button type="button" class="faq-clear-all-btn" style="display: none;" title="<?php esc_attr_e('Limpar filtros', 'faq-elementor'); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            <?php _e('LIMPAR', 'faq-elementor'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="faq-items-wrapper">
                <div class="faq-items">
                    <?php if ($faq_query->have_posts()) : ?>
                        <?php while ($faq_query->have_posts()) : $faq_query->the_post(); 
                            $post_tags = get_the_terms(get_the_ID(), 'faq_tag');
                            $tag_slugs = [];
                            if ($post_tags && !is_wp_error($post_tags)) {
                                foreach ($post_tags as $tag) {
                                    $tag_slugs[] = $tag->slug;
                                }
                            }
                        ?>
                            <div class="faq-item" data-id="<?php echo esc_attr(get_the_ID()); ?>" data-tags="<?php echo esc_attr(implode(',', $tag_slugs)); ?>">
                                <div class="faq-question-wrapper">
                                    <span class="faq-question"><?php the_title(); ?></span>
                                    <span class="faq-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="5" x2="12" y2="19" class="faq-icon-vertical"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </span>
                                </div>
                                <div class="faq-answer">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>

                <p class="faq-no-results" style="display: none;">
                    <?php _e('Nenhuma pergunta encontrada para sua busca.', 'faq-elementor'); ?>
                </p>
            </div>
        </div>

        <?php
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <#
        var layoutClass = settings.layout_style === 'two-columns' ? 'faq-two-columns' : 'faq-one-column';
        #>
        <div class="faq-container {{ layoutClass }}">
            <div class="faq-header">
                <# if (settings.title) { #>
                    <h2 class="faq-title">{{{ settings.title }}}</h2>
                <# } #>

                <# if (settings.show_search === 'yes') { #>
                    <div class="faq-search-wrapper">
                        <input 
                            type="text" 
                            class="faq-search-input" 
                            placeholder="{{ settings.search_placeholder }}"
                        >
                        <span class="faq-search-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="M21 21l-4.35-4.35"></path>
                            </svg>
                        </span>
                    </div>
                <# } #>

                <# if (settings.show_tags_filter === 'yes') { #>
                    <div class="faq-tags-filter">
                        <button type="button" class="faq-tag-btn">VALORES</button>
                        <button type="button" class="faq-tag-btn">INGRESSOS</button>
                        <button type="button" class="faq-tag-btn">VISITAS</button>
                        <button type="button" class="faq-tag-btn">HORÁRIOS</button>
                        <button type="button" class="faq-tag-btn">RESTAURANTE</button>
                        <button type="button" class="faq-tag-btn">LOJAS</button>
                    </div>
                <# } #>
            </div>

            <div class="faq-items-wrapper">
                <div class="faq-items">
                    <div class="faq-item">
                        <div class="faq-question-wrapper">
                            <span class="faq-question">Exemplo de pergunta 1</span>
                            <span class="faq-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" class="faq-icon-vertical"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </span>
                        </div>
                        <div class="faq-answer" style="display: none;">
                            <p>Esta é uma resposta de exemplo.</p>
                        </div>
                    </div>
                    <div class="faq-item active">
                        <div class="faq-question-wrapper">
                            <span class="faq-question">Exemplo de pergunta 2</span>
                            <span class="faq-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" class="faq-icon-vertical"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </span>
                        </div>
                        <div class="faq-answer">
                            <p>Esta é outra resposta de exemplo que está aberta.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question-wrapper">
                            <span class="faq-question">Exemplo de pergunta 3</span>
                            <span class="faq-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" class="faq-icon-vertical"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </span>
                        </div>
                        <div class="faq-answer" style="display: none;">
                            <p>Mais uma resposta de exemplo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
