# FAQ Elementor Plugin

Plugin de FAQ personalizado para WordPress com widget para Elementor.

## Descrição

Este plugin cria um sistema completo de Perguntas Frequentes (FAQ) com:

- **Custom Post Type** para gerenciar perguntas e respostas
- **Taxonomia de Tags** para categorizar as perguntas (Valores, Ingressos, Visitas, Horários, Restaurante, Lojas, etc.)
- **Widget do Elementor** para exibir o FAQ no editor visual
- **Filtro por tags** interativo
- **Design responsivo** com layout em duas colunas

## Instalação

1. Faça upload da pasta `faq-elementor` para `/wp-content/plugins/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Certifique-se de que o Elementor está instalado e ativado

## Como Usar

### 1. Cadastrar Perguntas

1. No painel do WordPress, vá em **FAQ > Adicionar Nova**
2. Digite a pergunta no título
3. Digite a resposta no editor de conteúdo
4. Adicione as tags apropriadas (ex: Valores, Ingressos, etc.)
5. Defina a ordem de exibição no campo lateral
6. Publique

### 2. Cadastrar Tags

1. Vá em **FAQ > Tags FAQ**
2. Adicione as categorias desejadas (ex: VALORES, INGRESSOS, VISITAS, HORÁRIOS, RESTAURANTE, LOJAS)

### 3. Usar no Elementor

1. Edite uma página com o Elementor
2. Procure pelo widget **"FAQ - Perguntas Frequentes"**
3. Arraste para a página
4. Configure:
   - Título da seção
   - Mostrar/ocultar filtro de tags
   - Filtrar por tags específicas
   - Número de perguntas
   - Ordenação
   - Cores e tipografia

## Personalização de Estilo

O widget oferece várias opções de estilo no Elementor:

- **Layout**: Uma ou duas colunas
- **Cores**: Fundo, título, tags, bordas, perguntas, respostas
- **Tipografia**: Fontes para título, perguntas e respostas
- **Espaçamentos**: Padding do container
- **Border Radius**: Para tags e itens do FAQ

## Estrutura de Arquivos

```
faq-elementor/
├── faq-elementor.php          # Arquivo principal do plugin
├── includes/
│   └── class-faq-widget.php   # Widget do Elementor
├── assets/
│   ├── css/
│   │   ├── faq-style.css      # Estilos do frontend
│   │   └── faq-editor.css     # Estilos do editor
│   └── js/
│       └── faq-script.js      # JavaScript do frontend
└── README.md
```

## Requisitos

- WordPress 5.0 ou superior
- PHP 7.4 ou superior
- Elementor (gratuito ou Pro)

## Changelog

### 1.0.0
- Versão inicial
- Custom Post Type para FAQ
- Taxonomia para tags
- Widget do Elementor com controles completos
- Filtro interativo por tags
- Design responsivo

## Autor

Desenvolvido por [Seu Nome]

## Licença

GPL v2 ou posterior
