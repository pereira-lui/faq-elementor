<?php
/**
 * FAQ Elementor Page Widget
 * Widget para página inteira de FAQ
 *
 * @package FAQ_Elementor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * FAQ Page Widget Class
 */
class FAQ_Elementor_Page_Widget extends \Elementor\Widget_Base {

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
        return 'faq_pda_page_widget';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('FAQ Página Completa', 'faq-elementor');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-single-page';
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
        return ['faq', 'perguntas', 'frequentes', 'accordion', 'questions', 'answers', 'busca', 'search', 'página', 'page', 'completa'];
    }

    /**
     * Get style dependencies
     * Isso faz o Elementor carregar o Swiper CSS apenas quando este widget é usado
     */
    public function get_style_depends() {
        return ['swiper', 'faq-elementor-style', 'faq-elementor-page-style'];
    }

    /**
     * Get script dependencies
     * Isso faz o Elementor carregar o Swiper JS apenas quando este widget é usado
     */
    public function get_script_depends() {
        return ['swiper', 'faq-elementor-script', 'faq-elementor-page-script'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {

        // ======================
        // CONTENT TAB
        // ======================

        // Hero Section
        $this->start_controls_section(
            'section_hero',
            [
                'label' => __('Cabeçalho Hero', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_hero',
            [
                'label' => __('Mostrar Seção Hero', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'hero_title',
            [
                'label' => __('Título Principal', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Central de Ajuda', 'faq-elementor'),
                'placeholder' => __('Digite o título', 'faq-elementor'),
                'condition' => [
                    'show_hero' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hero_subtitle',
            [
                'label' => __('Subtítulo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Encontre respostas para suas dúvidas mais frequentes', 'faq-elementor'),
                'placeholder' => __('Digite o subtítulo', 'faq-elementor'),
                'rows' => 2,
                'condition' => [
                    'show_hero' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'hero_alignment',
            [
                'label' => __('Alinhamento do Texto', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Esquerda', 'faq-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Centro', 'faq-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Direita', 'faq-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .faq-page-hero' => 'text-align: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-hero-content' => 'text-align: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-hero-title' => 'text-align: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-hero-subtitle' => 'text-align: {{VALUE}} !important;',
                ],
                'condition' => [
                    'show_hero' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_alignment',
            [
                'label' => __('Alinhamento do Campo de Busca', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Esquerda', 'faq-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Centro', 'faq-elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Direita', 'faq-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'prefix_class' => 'faq-search-align-',
                'condition' => [
                    'show_hero' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'hero_content_max_width',
            [
                'label' => __('Largura do Conteúdo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1400,
                    ],
                    '%' => [
                        'min' => 30,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 30,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 900,
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-hero-content' => 'max-width: {{SIZE}}{{UNIT}} !important; width: 100% !important;',
                ],
                'condition' => [
                    'show_hero' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Search Section
        $this->start_controls_section(
            'section_search',
            [
                'label' => __('Campo de Busca', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
                'default' => __('Digite sua pergunta ou palavra-chave...', 'faq-elementor'),
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'search_position',
            [
                'label' => __('Posição da Busca', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'hero',
                'options' => [
                    'hero' => __('Na Seção Hero', 'faq-elementor'),
                    'above_content' => __('Acima do Conteúdo', 'faq-elementor'),
                ],
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_width',
            [
                'label' => __('Largura do Campo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 30,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-wrapper' => 'max-width: {{SIZE}}{{UNIT}} !important; width: 100% !important;',
                ],
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Categories Section
        $this->start_controls_section(
            'section_categories',
            [
                'label' => __('Categorias/Tags', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_categories',
            [
                'label' => __('Mostrar Categorias', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'categories_layout',
            [
                'label' => __('Layout das Categorias', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'tabs',
                'options' => [
                    'tabs' => __('Abas Horizontais', 'faq-elementor'),
                    'sidebar' => __('Barra Lateral', 'faq-elementor'),
                    'grid' => __('Grade de Cards', 'faq-elementor'),
                ],
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_all_tab',
            [
                'label' => __('Mostrar Aba "Todos"', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'all_tab_label',
            [
                'label' => __('Texto da Aba "Todos"', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Todos', 'faq-elementor'),
                'condition' => [
                    'show_categories' => 'yes',
                    'show_all_tab' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_category_count',
            [
                'label' => __('Mostrar Contagem', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sticky_categories',
            [
                'label' => __('Categorias Sticky', 'faq-elementor'),
                'description' => __('Fixa as categorias no topo ao rolar a página', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // FAQ Content Section
        $this->start_controls_section(
            'section_faq_content',
            [
                'label' => __('Conteúdo FAQ', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Perguntas por Página', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 100,
                'step' => 1,
                'default' => 10,
                'description' => __('-1 para mostrar todas.', 'faq-elementor'),
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Mostrar Paginação', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __('Tipo de Paginação', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'load_more',
                'options' => [
                    'load_more' => __('Botão Carregar Mais', 'faq-elementor'),
                    'numbers' => __('Números', 'faq-elementor'),
                    'infinite' => __('Scroll Infinito', 'faq-elementor'),
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'load_more_text',
            [
                'label' => __('Texto do Botão', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Carregar Mais Perguntas', 'faq-elementor'),
                'condition' => [
                    'show_pagination' => 'yes',
                    'pagination_type' => 'load_more',
                ],
            ]
        );

        $this->add_control(
            'expand_first',
            [
                'label' => __('Expandir Primeira Pergunta', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'allow_multiple_open',
            [
                'label' => __('Permitir Múltiplas Abertas', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('Permitir que várias perguntas fiquem abertas ao mesmo tempo.', 'faq-elementor'),
            ]
        );

        $this->end_controls_section();

        // Contact CTA Section
        $this->start_controls_section(
            'section_contact_cta',
            [
                'label' => __('Seção de Contato', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_contact_cta',
            [
                'label' => __('Mostrar Seção de Contato', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'faq-elementor'),
                'label_off' => __('Não', 'faq-elementor'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'contact_title',
            [
                'label' => __('Título', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Não encontrou o que procurava?', 'faq-elementor'),
                'condition' => [
                    'show_contact_cta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'contact_description',
            [
                'label' => __('Descrição', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Nossa equipe está pronta para ajudar você.', 'faq-elementor'),
                'rows' => 2,
                'condition' => [
                    'show_contact_cta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'contact_button_text',
            [
                'label' => __('Texto do Botão', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Entre em Contato', 'faq-elementor'),
                'condition' => [
                    'show_contact_cta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'contact_button_link',
            [
                'label' => __('Link do Botão', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://seusite.com/contato', 'faq-elementor'),
                'default' => [
                    'url' => '/contato',
                ],
                'condition' => [
                    'show_contact_cta' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // ======================
        // STYLE TAB
        // ======================

        // Hero Style Section
        $this->start_controls_section(
            'section_hero_style',
            [
                'label' => __('Estilo Hero', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_hero' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hero_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-hero' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'hero_title_color',
            [
                'label' => __('Cor do Título', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-hero-title' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hero_title_typography',
                'label' => __('Tipografia do Título', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-page-hero-title',
            ]
        );

        $this->add_control(
            'hero_subtitle_color',
            [
                'label' => __('Cor do Subtítulo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-hero-subtitle' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hero_subtitle_typography',
                'label' => __('Tipografia do Subtítulo', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-page-hero-subtitle',
            ]
        );

        $this->add_responsive_control(
            'hero_padding',
            [
                'label' => __('Padding', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        // Search Style Section
        $this->start_controls_section(
            'section_search_style',
            [
                'label' => __('Estilo do Campo de Busca', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'search_typography',
                'label' => __('Tipografia', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-page-search-input',
            ]
        );

        $this->add_control(
            'search_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-input' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'search_text_color',
            [
                'label' => __('Cor do Texto', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-input' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-search-input::placeholder' => 'color: {{VALUE}} !important; opacity: 0.6 !important;',
                ],
            ]
        );

        $this->add_control(
            'search_border_color',
            [
                'label' => __('Cor da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-input' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_border_width',
            [
                'label' => __('Largura da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-input' => 'border-width: {{SIZE}}{{UNIT}} !important; border-style: solid !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_padding',
            [
                'label' => __('Padding', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_border_radius',
            [
                'label' => __('Border Radius', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-search-input' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'search_box_shadow',
                'selector' => '{{WRAPPER}} .faq-page-search-wrapper',
            ]
        );

        $this->end_controls_section();

        // Categories Style Section
        $this->start_controls_section(
            'section_categories_style',
            [
                'label' => __('Estilo das Categorias', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => __('Tipografia', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-page-container .faq-page-category-btn',
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => __('Padding', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_gap',
            [
                'label' => __('Espaçamento entre Tags', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper' => '--swiper-navigation-sides-offset: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .faq-page-category-slide' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_text_color',
            [
                'label' => __('Cor do Texto', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_hover_heading',
            [
                'label' => __('Estado Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'category_hover_bg_color',
            [
                'label' => __('Cor de Fundo Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_hover_text_color',
            [
                'label' => __('Cor do Texto Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_hover_border_color',
            [
                'label' => __('Cor da Borda Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn:hover' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_active_heading',
            [
                'label' => __('Estado Ativo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'category_active_bg_color',
            [
                'label' => __('Cor de Fundo Ativa', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn.active' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_active_text_color',
            [
                'label' => __('Cor do Texto Ativa', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn.active' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_active_border_color',
            [
                'label' => __('Cor da Borda Ativa', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn.active' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'category_border_heading',
            [
                'label' => __('Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'category_border_width',
            [
                'label' => __('Largura da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn' => 'border-width: {{SIZE}}{{UNIT}} !important; border-style: solid !important;',
                ],
            ]
        );

        $this->add_control(
            'category_border_color',
            [
                'label' => __('Cor da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label' => __('Border Radius', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container .faq-page-category-btn' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        // Navigation Buttons Style Section
        $this->start_controls_section(
            'section_nav_buttons_style',
            [
                'label' => __('Botões de Navegação', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_categories' => 'yes',
                    'categories_layout' => 'tabs',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_button_size',
            [
                'label' => __('Tamanho do Botão', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next, {{WRAPPER}} .faq-page-swiper-prev' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_button_icon_size',
            [
                'label' => __('Tamanho do Ícone', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next svg, {{WRAPPER}} .faq-page-swiper-prev svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'nav_button_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next, {{WRAPPER}} .faq-page-swiper-prev' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'nav_button_icon_color',
            [
                'label' => __('Cor do Ícone', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next, {{WRAPPER}} .faq-page-swiper-prev' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-swiper-next svg, {{WRAPPER}} .faq-page-swiper-prev svg' => 'stroke: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'nav_button_hover_heading',
            [
                'label' => __('Estado Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'nav_button_hover_bg_color',
            [
                'label' => __('Cor de Fundo Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next:hover, {{WRAPPER}} .faq-page-swiper-prev:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'nav_button_hover_icon_color',
            [
                'label' => __('Cor do Ícone Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next:hover, {{WRAPPER}} .faq-page-swiper-prev:hover' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-swiper-next:hover svg, {{WRAPPER}} .faq-page-swiper-prev:hover svg' => 'stroke: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_button_border_radius',
            [
                'label' => __('Border Radius', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-swiper-next, {{WRAPPER}} .faq-page-swiper-prev' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'nav_button_box_shadow',
                'selector' => '{{WRAPPER}} .faq-page-swiper-next, {{WRAPPER}} .faq-page-swiper-prev',
            ]
        );

        $this->end_controls_section();

        // FAQ Items Style Section
        $this->start_controls_section(
            'section_faq_items_style',
            [
                'label' => __('Estilo dos Itens FAQ', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'item_border_color',
            [
                'label' => __('Cor da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_border_width',
            [
                'label' => __('Largura da Borda', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item' => 'border-width: {{SIZE}}{{UNIT}} !important; border-style: solid !important;',
                ],
            ]
        );

        $this->add_responsive_control(
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
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Padding do Item', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-question-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_gap',
            [
                'label' => __('Espaçamento entre Itens', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-items' => 'gap: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'question_color',
            [
                'label' => __('Cor da Pergunta', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-question' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'question_typography',
                'label' => __('Tipografia da Pergunta', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-page-question',
            ]
        );

        $this->add_control(
            'answer_color',
            [
                'label' => __('Cor da Resposta', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-answer-content' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'answer_typography',
                'label' => __('Tipografia da Resposta', 'faq-elementor'),
                'selector' => '{{WRAPPER}} .faq-page-answer-content',
            ]
        );

        $this->add_responsive_control(
            'answer_padding',
            [
                'label' => __('Padding da Resposta', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-answer-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Cor do Ícone', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-icon' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-icon svg' => 'stroke: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Tamanho do Ícone', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-icon svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'heading_hover_active',
            [
                'label' => __('Hover e Item Ativo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_hover_bg_color',
            [
                'label' => __('Cor de Fundo no Hover', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item:hover' => 'background-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-question-wrapper:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'item_active_bg_color',
            [
                'label' => __('Cor de Fundo Item Aberto', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item.active' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'item_active_border_color',
            [
                'label' => __('Cor da Borda Item Aberto', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item.active' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'question_active_color',
            [
                'label' => __('Cor da Pergunta (Aberto)', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item.active .faq-page-question' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'icon_active_color',
            [
                'label' => __('Cor do Ícone (Aberto)', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .faq-page-item.active .faq-page-icon' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .faq-page-item.active .faq-page-icon svg' => 'stroke: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        // Contact CTA Style Section
        $this->start_controls_section(
            'section_contact_style',
            [
                'label' => __('Estilo da Seção de Contato', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_contact_cta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'contact_bg_color',
            [
                'label' => __('Cor de Fundo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .faq-page-contact-cta' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'contact_title_color',
            [
                'label' => __('Cor do Título', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a2e',
                'selectors' => [
                    '{{WRAPPER}} .faq-page-contact-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'contact_btn_bg_color',
            [
                'label' => __('Cor do Botão', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a2e',
                'selectors' => [
                    '{{WRAPPER}} .faq-page-contact-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'contact_btn_text_color',
            [
                'label' => __('Cor do Texto do Botão', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-page-contact-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'contact_btn_border_radius',
            [
                'label' => __('Border Radius do Botão', 'faq-elementor'),
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
                    '{{WRAPPER}} .faq-page-contact-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // General Page Style
        $this->start_controls_section(
            'section_page_style',
            [
                'label' => __('Estilo Geral da Página', 'faq-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'page_bg_color',
            [
                'label' => __('Cor de Fundo da Página', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .faq-page-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_max_width',
            [
                'label' => __('Largura Máxima do Conteúdo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 600,
                        'max' => 1600,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-content-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Padding do Conteúdo', 'faq-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '60',
                    'right' => '20',
                    'bottom' => '60',
                    'left' => '20',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .faq-page-main-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        // Get all tags for categories
        $all_tags = get_terms([
            'taxonomy' => 'faq_tag',
            'hide_empty' => true,
        ]);

        // Query arguments for initial load
        $args = [
            'post_type' => 'faq_item',
            'posts_per_page' => $settings['posts_per_page'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => 1,
        ];

        $faq_query = new WP_Query($args);
        $total_posts = $faq_query->found_posts;
        $max_pages = $faq_query->max_num_pages;

        // Get category layout class
        $categories_layout = isset($settings['categories_layout']) ? $settings['categories_layout'] : 'tabs';
        $layout_class = 'faq-page-layout-' . $categories_layout;
        $sticky_categories = isset($settings['sticky_categories']) ? $settings['sticky_categories'] : 'yes';
        ?>

        <div class="faq-page-container <?php echo esc_attr($layout_class); ?>" 
             data-widget-id="<?php echo esc_attr($widget_id); ?>"
             data-posts-per-page="<?php echo esc_attr($settings['posts_per_page']); ?>"
             data-pagination-type="<?php echo esc_attr($settings['pagination_type']); ?>"
             data-allow-multiple="<?php echo esc_attr($settings['allow_multiple_open']); ?>"
             data-max-pages="<?php echo esc_attr($max_pages); ?>"
             data-sticky-categories="<?php echo esc_attr($sticky_categories); ?>"
             data-current-page="1">

            <?php // Hero Section ?>
            <?php if ($settings['show_hero'] === 'yes') : ?>
                <div class="faq-page-hero">
                    <div class="faq-page-hero-content">
                        <?php if (!empty($settings['hero_title'])) : ?>
                            <h1 class="faq-page-hero-title"><?php echo esc_html($settings['hero_title']); ?></h1>
                        <?php endif; ?>

                        <?php if (!empty($settings['hero_subtitle'])) : ?>
                            <p class="faq-page-hero-subtitle"><?php echo esc_html($settings['hero_subtitle']); ?></p>
                        <?php endif; ?>

                        <?php // Search in Hero ?>
                        <?php if ($settings['show_search'] === 'yes' && $settings['search_position'] === 'hero') : ?>
                            <?php $this->render_search_field($settings); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="faq-page-main-content">
                <div class="faq-page-content-wrapper">

                    <?php // Search above content ?>
                    <?php if ($settings['show_search'] === 'yes' && $settings['search_position'] === 'above_content') : ?>
                        <?php $this->render_search_field($settings); ?>
                    <?php endif; ?>

                    <?php // Categories/Tags ?>
                    <?php if ($settings['show_categories'] === 'yes' && !is_wp_error($all_tags) && !empty($all_tags)) : ?>
                        <div class="faq-page-categories faq-page-categories-<?php echo esc_attr($categories_layout); ?>">
                            <?php if ($categories_layout === 'sidebar') : ?>
                                <div class="faq-page-sidebar">
                                    <h3 class="faq-page-sidebar-title"><?php _e('Categorias', 'faq-elementor'); ?></h3>
                            <?php endif; ?>

                            <?php if ($categories_layout === 'tabs') : ?>
                            <div class="faq-page-category-slider">
                                <!-- Swiper Container -->
                                <div class="swiper faq-page-swiper">
                                    <div class="swiper-wrapper faq-page-category-list">
                                        <?php if ($settings['show_all_tab'] === 'yes') : ?>
                                            <div class="swiper-slide faq-page-category-slide">
                                                <button type="button" class="faq-page-category-btn active" data-category="all">
                                                    <?php echo esc_html($settings['all_tab_label']); ?>
                                                    <?php if ($settings['show_category_count'] === 'yes') : ?>
                                                        <span class="faq-page-category-count"><?php echo esc_html($total_posts); ?></span>
                                                    <?php endif; ?>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php foreach ($all_tags as $tag) : ?>
                                            <div class="swiper-slide faq-page-category-slide">
                                                <button type="button" class="faq-page-category-btn" data-category="<?php echo esc_attr($tag->slug); ?>">
                                                    <?php echo esc_html($tag->name); ?>
                                                    <?php if ($settings['show_category_count'] === 'yes') : ?>
                                                        <span class="faq-page-category-count"><?php echo esc_html($tag->count); ?></span>
                                                    <?php endif; ?>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Navegação do Swiper -->
                                <div class="faq-page-category-nav">
                                    <button type="button" class="faq-page-swiper-prev" aria-label="<?php esc_attr_e('Anterior', 'faq-elementor'); ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </button>
                                    <button type="button" class="faq-page-swiper-next" aria-label="<?php esc_attr_e('Próximo', 'faq-elementor'); ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <?php else : ?>
                            <div class="faq-page-category-list">
                                <?php if ($settings['show_all_tab'] === 'yes') : ?>
                                    <button type="button" class="faq-page-category-btn active" data-category="all">
                                        <?php echo esc_html($settings['all_tab_label']); ?>
                                        <?php if ($settings['show_category_count'] === 'yes') : ?>
                                            <span class="faq-page-category-count"><?php echo esc_html($total_posts); ?></span>
                                        <?php endif; ?>
                                    </button>
                                <?php endif; ?>

                                <?php foreach ($all_tags as $tag) : ?>
                                    <button type="button" class="faq-page-category-btn" data-category="<?php echo esc_attr($tag->slug); ?>">
                                        <?php echo esc_html($tag->name); ?>
                                        <?php if ($settings['show_category_count'] === 'yes') : ?>
                                            <span class="faq-page-category-count"><?php echo esc_html($tag->count); ?></span>
                                        <?php endif; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($categories_layout === 'sidebar') : ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php // FAQ Items ?>
                    <div class="faq-page-items-wrapper">
                        <div class="faq-page-items">
                            <?php if ($faq_query->have_posts()) : ?>
                                <?php 
                                $first_item = true;
                                while ($faq_query->have_posts()) : $faq_query->the_post(); 
                                    $post_tags = get_the_terms(get_the_ID(), 'faq_tag');
                                    $tag_slugs = [];
                                    if ($post_tags && !is_wp_error($post_tags)) {
                                        foreach ($post_tags as $tag) {
                                            $tag_slugs[] = $tag->slug;
                                        }
                                    }
                                    $is_active = ($settings['expand_first'] === 'yes' && $first_item);
                                ?>
                                    <div class="faq-page-item <?php echo $is_active ? 'active' : ''; ?>" 
                                         data-id="<?php echo esc_attr(get_the_ID()); ?>" 
                                         data-tags="<?php echo esc_attr(implode(',', $tag_slugs)); ?>">
                                        <div class="faq-page-question-wrapper">
                                            <span class="faq-page-question"><?php the_title(); ?></span>
                                            <span class="faq-page-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="faq-page-answer" <?php echo $is_active ? 'style="max-height: 1000px;"' : ''; ?>>
                                            <div class="faq-page-answer-content">
                                                <?php the_content(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                    $first_item = false;
                                endwhile; 
                                ?>
                            <?php else : ?>
                                <p class="faq-page-no-results"><?php _e('Nenhuma pergunta cadastrada ainda.', 'faq-elementor'); ?></p>
                            <?php endif; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>

                        <p class="faq-page-no-results faq-page-search-no-results" style="display: none;">
                            <?php _e('Nenhuma pergunta encontrada para sua busca.', 'faq-elementor'); ?>
                        </p>

                        <?php // Pagination ?>
                        <?php if ($settings['show_pagination'] === 'yes' && $max_pages > 1) : ?>
                            <div class="faq-page-pagination">
                                <?php if ($settings['pagination_type'] === 'load_more') : ?>
                                    <button type="button" class="faq-page-load-more-btn">
                                        <span class="faq-page-load-more-text"><?php echo esc_html($settings['load_more_text']); ?></span>
                                        <span class="faq-page-load-more-loading" style="display: none;"><?php _e('Carregando...', 'faq-elementor'); ?></span>
                                        <span class="faq-page-load-more-spinner" style="display: none;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83">
                                                    <animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/>
                                                </path>
                                            </svg>
                                        </span>
                                    </button>
                                <?php elseif ($settings['pagination_type'] === 'numbers') : ?>
                                    <div class="faq-page-pagination-numbers">
                                        <?php for ($i = 1; $i <= $max_pages; $i++) : ?>
                                            <button type="button" class="faq-page-pagination-num <?php echo $i === 1 ? 'active' : ''; ?>" data-page="<?php echo esc_attr($i); ?>">
                                                <?php echo esc_html($i); ?>
                                            </button>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <?php // Contact CTA Section ?>
            <?php if ($settings['show_contact_cta'] === 'yes') : ?>
                <div class="faq-page-contact-cta">
                    <div class="faq-page-contact-content">
                        <?php if (!empty($settings['contact_title'])) : ?>
                            <h3 class="faq-page-contact-title"><?php echo esc_html($settings['contact_title']); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($settings['contact_description'])) : ?>
                            <p class="faq-page-contact-description"><?php echo esc_html($settings['contact_description']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($settings['contact_button_text']) && !empty($settings['contact_button_link']['url'])) : ?>
                            <a href="<?php echo esc_url($settings['contact_button_link']['url']); ?>" 
                               class="faq-page-contact-btn"
                               <?php echo $settings['contact_button_link']['is_external'] ? 'target="_blank"' : ''; ?>
                               <?php echo $settings['contact_button_link']['nofollow'] ? 'rel="nofollow"' : ''; ?>>
                                <?php echo esc_html($settings['contact_button_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }

    /**
     * Render search field helper
     */
    private function render_search_field($settings) {
        ?>
        <div class="faq-page-search-wrapper">
            <input 
                type="text" 
                class="faq-page-search-input" 
                placeholder="<?php echo esc_attr($settings['search_placeholder']); ?>"
                autocomplete="off"
            >
            <button type="button" class="faq-page-search-clear" style="display: none;" title="<?php esc_attr_e('Limpar busca', 'faq-elementor'); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <span class="faq-page-search-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
            </span>
            <span class="faq-page-search-loading" style="display: none;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32">
                        <animate attributeName="stroke-dashoffset" dur="1s" values="32;0" repeatCount="indefinite"/>
                    </circle>
                </svg>
            </span>
        </div>
        <?php
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <#
        var layoutClass = 'faq-page-layout-' + (settings.categories_layout || 'tabs');
        #>
        <div class="faq-page-container {{ layoutClass }}">

            <# if (settings.show_hero === 'yes') { #>
                <div class="faq-page-hero">
                    <div class="faq-page-hero-content">
                        <# if (settings.hero_title) { #>
                            <h1 class="faq-page-hero-title">{{{ settings.hero_title }}}</h1>
                        <# } #>

                        <# if (settings.hero_subtitle) { #>
                            <p class="faq-page-hero-subtitle">{{{ settings.hero_subtitle }}}</p>
                        <# } #>

                        <# if (settings.show_search === 'yes' && settings.search_position === 'hero') { #>
                            <div class="faq-page-search-wrapper">
                                <input type="text" class="faq-page-search-input" placeholder="{{ settings.search_placeholder }}">
                                <span class="faq-page-search-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="M21 21l-4.35-4.35"></path>
                                    </svg>
                                </span>
                            </div>
                        <# } #>
                    </div>
                </div>
            <# } #>

            <div class="faq-page-main-content">
                <div class="faq-page-content-wrapper">

                    <# if (settings.show_search === 'yes' && settings.search_position === 'above_content') { #>
                        <div class="faq-page-search-wrapper">
                            <input type="text" class="faq-page-search-input" placeholder="{{ settings.search_placeholder }}">
                            <span class="faq-page-search-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="M21 21l-4.35-4.35"></path>
                                </svg>
                            </span>
                        </div>
                    <# } #>

                    <# if (settings.show_categories === 'yes') { #>
                        <div class="faq-page-categories faq-page-categories-{{ settings.categories_layout }}">
                            <# if (settings.categories_layout === 'sidebar') { #>
                                <div class="faq-page-sidebar">
                                    <h3 class="faq-page-sidebar-title">Categorias</h3>
                            <# } #>

                            <# if (settings.categories_layout === 'tabs') { #>
                            <div class="faq-page-category-slider">
                                <div class="swiper faq-page-swiper">
                                    <div class="swiper-wrapper faq-page-category-list">
                                        <# if (settings.show_all_tab === 'yes') { #>
                                            <div class="swiper-slide faq-page-category-slide">
                                                <button type="button" class="faq-page-category-btn active" data-category="all">
                                                    {{ settings.all_tab_label }}
                                                    <# if (settings.show_category_count === 'yes') { #>
                                                        <span class="faq-page-category-count">15</span>
                                                    <# } #>
                                                </button>
                                            </div>
                                        <# } #>
                                        <div class="swiper-slide faq-page-category-slide">
                                            <button type="button" class="faq-page-category-btn">
                                                Ingressos
                                                <# if (settings.show_category_count === 'yes') { #>
                                                    <span class="faq-page-category-count">5</span>
                                                <# } #>
                                            </button>
                                        </div>
                                        <div class="swiper-slide faq-page-category-slide">
                                            <button type="button" class="faq-page-category-btn">
                                                Horários
                                                <# if (settings.show_category_count === 'yes') { #>
                                                    <span class="faq-page-category-count">4</span>
                                                <# } #>
                                            </button>
                                        </div>
                                        <div class="swiper-slide faq-page-category-slide">
                                            <button type="button" class="faq-page-category-btn">
                                                Visitas
                                                <# if (settings.show_category_count === 'yes') { #>
                                                    <span class="faq-page-category-count">6</span>
                                                <# } #>
                                            </button>
                                        </div>
                                        <div class="swiper-slide faq-page-category-slide">
                                            <button type="button" class="faq-page-category-btn">
                                                Restaurante
                                                <# if (settings.show_category_count === 'yes') { #>
                                                    <span class="faq-page-category-count">3</span>
                                                <# } #>
                                            </button>
                                        </div>
                                        <div class="swiper-slide faq-page-category-slide">
                                            <button type="button" class="faq-page-category-btn">
                                                Lojas
                                                <# if (settings.show_category_count === 'yes') { #>
                                                    <span class="faq-page-category-count">2</span>
                                                <# } #>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="faq-page-category-nav">
                                    <button type="button" class="faq-page-swiper-prev">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </button>
                                    <button type="button" class="faq-page-swiper-next">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <# } else { #>
                            <div class="faq-page-category-list">
                                <# if (settings.show_all_tab === 'yes') { #>
                                    <button type="button" class="faq-page-category-btn active" data-category="all">
                                        {{ settings.all_tab_label }}
                                        <# if (settings.show_category_count === 'yes') { #>
                                            <span class="faq-page-category-count">15</span>
                                        <# } #>
                                    </button>
                                <# } #>
                                <button type="button" class="faq-page-category-btn">
                                    Ingressos
                                    <# if (settings.show_category_count === 'yes') { #>
                                        <span class="faq-page-category-count">5</span>
                                    <# } #>
                                </button>
                                <button type="button" class="faq-page-category-btn">
                                    Horários
                                    <# if (settings.show_category_count === 'yes') { #>
                                        <span class="faq-page-category-count">4</span>
                                    <# } #>
                                </button>
                                <button type="button" class="faq-page-category-btn">
                                    Visitas
                                    <# if (settings.show_category_count === 'yes') { #>
                                        <span class="faq-page-category-count">6</span>
                                    <# } #>
                                </button>
                                <button type="button" class="faq-page-category-btn">
                                    Restaurante
                                    <# if (settings.show_category_count === 'yes') { #>
                                        <span class="faq-page-category-count">3</span>
                                    <# } #>
                                </button>
                                <button type="button" class="faq-page-category-btn">
                                    Lojas
                                    <# if (settings.show_category_count === 'yes') { #>
                                        <span class="faq-page-category-count">2</span>
                                    <# } #>
                                </button>
                            </div>
                            <# } #>

                            <# if (settings.categories_layout === 'sidebar') { #>
                                </div>
                            <# } #>
                        </div>
                    <# } #>

                    <div class="faq-page-items-wrapper">
                        <div class="faq-page-items">
                            <div class="faq-page-item {{ settings.expand_first === 'yes' ? 'active' : '' }}">
                                <div class="faq-page-question-wrapper">
                                    <span class="faq-page-question">Exemplo de pergunta 1 - Como funciona?</span>
                                    <span class="faq-page-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </div>
                                <div class="faq-page-answer">
                                    <div class="faq-page-answer-content">
                                        <p>Esta é uma resposta de exemplo. Aqui você pode adicionar informações detalhadas sobre a pergunta.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="faq-page-item">
                                <div class="faq-page-question-wrapper">
                                    <span class="faq-page-question">Exemplo de pergunta 2 - Quais são os horários?</span>
                                    <span class="faq-page-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </div>
                                <div class="faq-page-answer">
                                    <div class="faq-page-answer-content">
                                        <p>Outra resposta de exemplo com mais detalhes.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="faq-page-item">
                                <div class="faq-page-question-wrapper">
                                    <span class="faq-page-question">Exemplo de pergunta 3 - Onde posso comprar?</span>
                                    <span class="faq-page-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </div>
                                <div class="faq-page-answer">
                                    <div class="faq-page-answer-content">
                                        <p>Mais uma resposta de exemplo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <# if (settings.show_pagination === 'yes') { #>
                            <div class="faq-page-pagination">
                                <# if (settings.pagination_type === 'load_more') { #>
                                    <button type="button" class="faq-page-load-more-btn">
                                        {{ settings.load_more_text }}
                                    </button>
                                <# } else if (settings.pagination_type === 'numbers') { #>
                                    <div class="faq-page-pagination-numbers">
                                        <button type="button" class="faq-page-pagination-num active">1</button>
                                        <button type="button" class="faq-page-pagination-num">2</button>
                                        <button type="button" class="faq-page-pagination-num">3</button>
                                    </div>
                                <# } #>
                            </div>
                        <# } #>
                    </div>

                </div>
            </div>

            <# if (settings.show_contact_cta === 'yes') { #>
                <div class="faq-page-contact-cta">
                    <div class="faq-page-contact-content">
                        <# if (settings.contact_title) { #>
                            <h3 class="faq-page-contact-title">{{{ settings.contact_title }}}</h3>
                        <# } #>

                        <# if (settings.contact_description) { #>
                            <p class="faq-page-contact-description">{{{ settings.contact_description }}}</p>
                        <# } #>

                        <# if (settings.contact_button_text) { #>
                            <a href="#" class="faq-page-contact-btn">{{ settings.contact_button_text }}</a>
                        <# } #>
                    </div>
                </div>
            <# } #>

        </div>
        <?php
    }
}
