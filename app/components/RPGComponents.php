<?php

class RPGComponents {
    
    /**
     * Renderiza um card de funcionalidade do sistema
     * 
     * @param array $config - Configurações do card:
     *  - title: Título do card
     *  - description: Descrição do card
     *  - icon: Nome do arquivo SVG (sem extensão)
     *  - color: Cor do tema (primary, secondary, accent, purple)
     *  - action: Nome da ação para o JavaScript
     *  - buttonText: Texto do botão
     */
    public static function featureCard($config) {
        // Configurações padrão
        $defaults = [
            'title' => 'Título do Card',
            'description' => 'Descrição do card',
            'icon' => 'sword',
            'color' => 'primary',
            'action' => 'feature',
            'buttonText' => 'Ver Mais →'
        ];
        
        $config = array_merge($defaults, $config);
        
        // Definir cores baseados no tipo
        $colorConfig = self::getColorConfig($config['color']);
        
        return "
        <!-- {$config['title']} -->
        <div class=\"text-center space-y-4 p-6 rounded-lg bg-surface/50 border border-border hover:border-{$colorConfig['borderColor']} transition-all\">
            <div class=\"w-16 h-16 mx-auto {$colorConfig['bgClass']} rounded-full flex items-center justify-center {$colorConfig['hoverBgClass']} hover:scale-110 transition-all\">
                <img src=\"img/icons-1x1/lorc/{$config['icon']}.svg\" alt=\"{$config['title']}\" class=\"w-8 h-8 icon-{$config['color']} hover:scale-110 transition-transform\">
            </div>
            <h3 class=\"font-heading text-lg font-semibold text-primary\">{$config['title']}</h3>
            <p class=\"text-text/70 text-sm\">
                {$config['description']}
            </p>
            <button onclick=\"systemToasts.featureComingSoon('{$config['action']}')\" 
                    class=\"text-{$colorConfig['textColor']} hover:text-{$colorConfig['hoverColor']} font-heading font-medium text-sm transition-colors\">
                {$config['buttonText']}
            </button>
        </div>";
    }
    
    /**
     * Configura cores baseados no tipo de cor
     */
    private static function getColorConfig($colorType) {
        $configs = [
            'primary' => [
                'borderColor' => 'yellow-600',
                'bgClass' => 'bg-yellow-500/10',
                'hoverBgClass' => 'hover:bg-yellow-500/20',
                'textColor' => 'yellow-600',
                'hoverColor' => 'yellow-500'
            ],
            'secondary' => [
                'borderColor' => 'emerald-500',
                'bgClass' => 'bg-emerald-500/10',
                'hoverBgClass' => 'hover:bg-emerald-500/20',
                'textColor' => 'emerald-600',
                'hoverColor' => 'emerald-500'
            ],
            'accent' => [
                'borderColor' => 'red-500',
                'bgClass' => 'bg-red-500/10',
                'hoverBgClass' => 'hover:bg-red-500/20',
                'textColor' => 'red-600', 
                'hoverColor' => 'red-500'
            ],
            'purple' => [
                'borderColor' => 'purple-500',
                'bgClass' => 'bg-purple-500/10',
                'hoverBgClass' => 'hover:bg-purple-500/20',
                'textColor' => 'purple-600',
                'hoverColor' => 'purple-500'
            ]
        ];
        
        return $configs[$colorType] ?? $configs['primary'];
    }
    
    /**
     * Renderiza um botão padrão do sistema
     * 
     * @param array $config - Configurações do botão:
     *  - text: Texto do botão
     *  - type: Tipo (primary, secondary, outline)
     *  - onclick: Função JavaScript para click
     *  - disabled: Se está desabilitado
     *  - icon: Ícone opcional
     */
    public static function button($config) {
        $defaults = [
            'text' => 'Botão',
            'type' => 'primary',
            'onclick' => '',
            'disabled' => false,
            'icon' => null,
            'classes' => ''
        ];
        
        $config = array_merge($defaults, $config);
        
        $buttonClass = "btn-{$config['type']}";
        $disabledAttr = $config['disabled'] ? 'disabled' : '';
        $onclickAttr = $config['onclick'] ? "onclick=\"{$config['onclick']}\"" : '';
        
        $iconHtml = '';
        if ($config['icon']) {
            $iconHtml = "<img src=\"img/icons-1x1/lorc/{$config['icon']}.svg\" alt=\"\" class=\"w-4 h-4 icon-white mr-2\">";
        }
        
        return "<button class=\"{$buttonClass} {$config['classes']}\" {$onclickAttr} {$disabledAttr}>
                    {$iconHtml}{$config['text']}
                </button>";
    }
    
    /**
     * Renderiza um card básico
     */
    public static function card($title, $content, $classes = '') {
        return "
        <div class=\"card {$classes}\">
            <h3 class=\"card-title\">{$title}</h3>
            <div class=\"card-description\">{$content}</div>
        </div>";
    }
    
    /**
     * Renderiza um card de estatística com borda lateral
     */
    public static function statCard($config) {
        $defaults = [
            'value' => '0',
            'label' => 'Estatística',
            'description' => 'Descrição',
            'color' => 'primary'
        ];
        
        $config = array_merge($defaults, $config);
        
        return "
        <div class=\"card border-l-4 border-{$config['color']} text-center\">
            <div class=\"text-3xl font-bold text-accent mb-2\">{$config['value']}</div>
            <h4 class=\"card-title mb-1\">{$config['label']}</h4>
            <p class=\"text-text/60 text-sm\">{$config['description']}</p>
        </div>";
    }
    
    /**
     * Renderiza um alerta/aviso
     */
    public static function alert($message, $type = 'info', $title = null) {
        $colors = [
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700'
        ];
        
        $colorClass = $colors[$type] ?? $colors['info'];
        $titleHtml = $title ? "<strong>{$title}</strong> " : '';
        
        return "<div class=\"{$colorClass} px-4 py-3 rounded-lg border\">
                    {$titleHtml}{$message}
                </div>";
    }
}

?>
