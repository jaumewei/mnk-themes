<?php defined('ABSPATH') or die;
/**
 * Widget demo para probar el entorno
 */
class CodersYoutubeWidget extends \CODERS\WidgetBase {
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Video Youtube' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Url a tu video Youtube' , 'coders_theme_manager' );
    }
    /**
     * @return \YoutubeWidget
     */
    protected final function registerWidgetInputs() {
        
        return $this->inputRegister('video',
                self::TYPE_TEXT , '',
                __('URL Video Youtube','coders_theme_manager'));
    }
    
    
    /**
     * Mostrar video
     * @param array $instance
     * @param array $args
     */
    protected function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        
        print $widget['video'];
    }
}


