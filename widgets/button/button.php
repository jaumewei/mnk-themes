<?php defined('ABSPATH') or die;
/**
 * Botón
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
class CodersButtonWidget extends \CODERS\WidgetBase{
    
    const TYPE_BUTTON = 'button';
    const TYPE_LINK = 'link';
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Bot&oacute;n' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Bot&oacute;n y enlace personalizado' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersButtonWidget
     */
    protected final function registerWidgetInputs() {
        return $this->inputRegister( 'url', parent::TYPE_TEXT, '', __('Url','coders_theme_manager'))
                ->inputRegister('text', parent::TYPE_TEXT, __('Bot&oacute;n','coders_theme_manager'), __('Texto','coders_theme_manager'))
                ->inputRegister('target', parent::TYPE_SELECT, '_self', __('Destino','coders_theme_manager'))
                ->inputRegister('type', parent::TYPE_SELECT, 'button', __('Tipo','coders_theme_manager'));
    }

    /**
     * @return array
     */
    protected final function getTargetOptions(){
        return array(
            '_self' => __('Misma ventana','coders_theme_manager'),
            '_blank' => __('Nueva ventana','coders_theme_manager'),
            //
        );
    }
    /**
     * @return array
     */
    protected final function getTypeOptions(){
        return array(
            'link' => __('Enlace','coders_button_widget'),
            'button' => __('Bot&oacute;n','coders_button_widget'),
        );
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display($instance, $args = null) {
        
        //agregar opciones de personalización (vistas externas)
        
        $widget = $this->inputImport($instance);
        
        printf('<a class="%s" href="%s" target="%s">%s</a>',
                $widget['type'],
                $widget['url'],
                $widget['target'],
                $widget['text']);
    }
}



