<?php defined('ABSPATH') or die;
/**
 * Título
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
final class CodersTitleWidget extends \CODERS\WidgetBase {
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


