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
     * Get widget name
     */
    public function get_name() {
        return 'faq_widget';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('FAQ - Perguntas Frequentes', 'faq-elementor');
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
        return ['general'];
    }

    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['faq', 'perguntas', 'frequentes', 'accordion', 'questions', 'answers'];
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
            'show_tags_filter',
            [
                'label' => __('Mostrar Filtro de Tags', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // Get FAQ Tags
        $faq_tags = get_terms([
            'taxonomy' => 'faq_tag',
            'hide_empty' => false,
        ]);

        $tags_options = [];
        if (!is_wp_error($faq_tags) && !empty($faq_tags)) {
            foreach ($faq_tags as $tag) {
                $tags_options[$tag->term_id] = $tag->name;
            }
        }

        $this->add_control(
            'selected_tags',
            [
                'label' => __('Filtrar por Tags', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $tags_options,
                'description' => __('Deixe vazio para mostrar todas as perguntas.', 'faq-elementor'),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Número de Perguntas', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 100,
                'step' => 1,
                'default' => -1,
                'description' => __('-1 para mostrar todas.', 'faq-elementor'),
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Ordenar por', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'menu_order',
                'options' => [
                    'menu_order' => __('Ordem Personalizada', 'faq-elementor'),
                    'date' => __('Data', 'faq-elementor'),
                    'title' => __('Título', 'faq-elementor'),
                    'rand' => __('Aleatório', 'faq-elementor'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Ordem', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => [
                    'ASC' => __('Crescente', 'faq-elementor'),
                    'DESC' => __('Decrescente', 'faq-elementor'),
                ],
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
                'default' => '#1a1a2e',
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
                    'top' => '60',
                    'right' => '40',
                    'bottom' => '60',
                    'left' => '40',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        // Query arguments
        $args = [
            'post_type' => 'faq_item',
            'posts_per_page' => $settings['posts_per_page'],
            'order' => $settings['order'],
        ];

        // Handle ordering
        if ($settings['orderby'] === 'menu_order') {
            $args['meta_key'] = '_faq_order';
            $args['orderby'] = 'meta_value_num';
        } else {
            $args['orderby'] = $settings['orderby'];
        }

        // Filter by selected tags
        if (!empty($settings['selected_tags'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'faq_tag',
                    'field' => 'term_id',
                    'terms' => $settings['selected_tags'],
                ],
            ];
        }

        $faq_query = new WP_Query($args);

        // Get all tags for filter
        $all_tags = get_terms([
            'taxonomy' => 'faq_tag',
            'hide_empty' => true,
        ]);

        $layout_class = $settings['layout_style'] === 'two-columns' ? 'faq-two-columns' : 'faq-one-column';
        ?>

        <div class="faq-container <?php echo esc_attr($layout_class); ?>">
            <div class="faq-header">
                <?php if (!empty($settings['title'])) : ?>
                    <h2 class="faq-title"><?php echo esc_html($settings['title']); ?></h2>
                <?php endif; ?>

                <?php if ($settings['show_tags_filter'] === 'yes' && !is_wp_error($all_tags) && !empty($all_tags)) : ?>
                    <div class="faq-tags-filter">
                        <?php foreach ($all_tags as $tag) : ?>
                            <button type="button" class="faq-tag-btn" data-tag="<?php echo esc_attr($tag->slug); ?>">
                                <?php echo esc_html(strtoupper($tag->name)); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="faq-items-wrapper">
                <?php if ($faq_query->have_posts()) : ?>
                    <div class="faq-items">
                        <?php while ($faq_query->have_posts()) : $faq_query->the_post(); 
                            $post_tags = get_the_terms(get_the_ID(), 'faq_tag');
                            $tag_classes = '';
                            $tag_slugs = [];
                            if ($post_tags && !is_wp_error($post_tags)) {
                                foreach ($post_tags as $tag) {
                                    $tag_slugs[] = $tag->slug;
                                }
                                $tag_classes = implode(' ', $tag_slugs);
                            }
                        ?>
                            <div class="faq-item" data-tags="<?php echo esc_attr(implode(',', $tag_slugs)); ?>">
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
                    </div>
                <?php else : ?>
                    <p class="faq-no-items"><?php _e('Nenhuma pergunta frequente encontrada.', 'faq-elementor'); ?></p>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
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
                    <div class="faq-item">
                        <div class="faq-question-wrapper">
                            <span class="faq-question">Exemplo de pergunta 4</span>
                            <span class="faq-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" class="faq-icon-vertical"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </span>
                        </div>
                        <div class="faq-answer" style="display: none;">
                            <p>Última resposta de exemplo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
