<?php defined('ABSPATH') or die;
/**
 * Título
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
final class CodersSpacerWidget extends \CODERS\WidgetBase {
    
    const ORIENTATION_HORIZONTAL = 'horizontal';
    const ORIENTATION_VERTICAL = 'vertical';
    /**
     * @return \CodersSpacerWidget
     */
    protected final function registerWidgetInputs() {
        return $this->inputRegister('size', parent::TYPE_NUMBER, 10,
                        __('Tamaño','coders_theme_manager'))
                ->inputRegister('orientation', parent::TYPE_SELECT, self::ORIENTATION_HORIZONTAL,
                        __('Orientaci&oacute;n','coders_theme_manager'))
                ->inputRegister('class',parent::TYPE_TEXT,'',
                        __('Clase CSS','coders_theme_manager'));
    }
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Separador' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'T&iacute;tulo simple' , 'coders_theme_manager' );
    }
    /**
     * @return array
     */
    protected final function getOrientationOptions(){
        return array(
            self::ORIENTATION_HORIZONTAL => __('Horizontal','coders_theme_manager'),
            self::ORIENTATION_VERTICAL => __('Vertical','coders_theme_manager')
        );
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display( $instance, $args = null ) {
        
        $widget = $this->inputImport($instance);
        
        $style = $widget['orientation'] === self::ORIENTATION_VERTICAL ?
                sprintf('height:%spx',$widget['size']):
                sprintf('width:%spx',$widget['size']);
        
        printf('<div class="spacer %s orientation-%s" style="%s"></div>',
                $widget['class'],
                $widget['orientation'],
                $style);
    }
}


