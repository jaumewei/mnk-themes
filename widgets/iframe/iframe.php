<?php
/**
 * Un iframe simple para mostrar evitando tener que incrustar codigo guarro y 
 * de cualquier manera
 */
class CodersIframeWidget extends \CODERS\WidgetBase{
    
    const WIDGET_ID = 'coders_iframe';
    const WIDGET_TITLE = 'title';
    const WIDGET_URL = 'url';
    const WIDGET_BORDER = 'border';
    const WIDGET_FULL_SCREEN = 'fullscreen';
    const WIDGET_SHOW_TITLE = 'show_title';
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Iframe' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Agrega un iframe mediante url' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersIframeWidget
     */
    protected final function registerWidgetInputs() {
        return $this->inputRegister('title',
                parent::TYPE_TEXT, '',
                __('T&iacute;tulo','coders_theme_manager'))
            ->inputRegister('url',
                parent::TYPE_TEXT, '',
                __('URL','coders_theme_manager'));
    }

    /**
     * Contenido del widget
     * @param array $instance
     * @param array $args
     */
    function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        
        if(strlen($widget['url'])){
            if(strlen($widget['title'])){
                print $args['before_title'];
                print $widget['title'];
                print $args['after_title'];
            }

            printf('<iframe src="%s"></iframe>',$widget['url']);
        }
        else{
            printf('<!-- %s -->',__('No se ha definido una url','coders_theme_manager'));
        }
    }
}
