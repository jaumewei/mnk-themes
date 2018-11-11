<?php defined('ABSPATH') or die;
/**
 * Widget demo para probar el entorno
 */
final class CodersTitleWidget extends \CODERS\WidgetBase {
    
    const TITLE_H1 = 'h1';
    const TITLE_H2 = 'h2';
    const TITLE_H3 = 'h3';
    const TITLE_H4 = 'h4';
    
    const TARGET_SELF = '_self';
    const TARGET_BLANK = '_blank';
    const TARGET_AUTO = '_auto';
    const TARGET_ANCHOR = 'anchor';
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'T&iacute;tulo' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'T&iacute;tulo simple' , 'coders_theme_manager' );
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display( $instance, $args = null ) {
        
        $widget = $this->inputImport($instance);
        
        print $args['before_title'] . $widget['title'] . $args['after_title'];
    }
}


