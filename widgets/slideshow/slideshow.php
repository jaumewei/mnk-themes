<?php defined('ABSPATH') or die;
/**
 * Widget de Slideshow
 */
class CodersSlideshowWidget extends \CODERS\WidgetBase{
    
    const ORIENTATION_HORIZONTAL = 'horizontal';
    
    const ORIENTATION_VERTICAL = 'vertical';

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Diapositivas de Im&aacute;genes' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Widget CODERS de diapositivas de im&aacute;genes' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersSlideshowWidget
     */
    protected final function registerWidgetInputs() {
        
        return $this->inputRegister('gallery',
                    parent::TYPE_MEDIA_LIST, '',
                    __('Selecci&oacute;n de diapositivas','coders_theme_manager'))
            ->inputRegister('orientation',
                    parent::TYPE_SELECT, self::ORIENTATION_HORIZONTAL,
                    __('Orientaci&oacute;n','coders_theme_manager'));
    }
    
    /**
     * @return array
     */
    protected final function getOrientationOptions(){
        return array(
            self::ORIENTATION_HORIZONTAL => __('Horizontal','coders_theme_manager'),
            self::ORIENTATION_VERTICAL => __('Vertical','coders_theme_manager'),
        );
    }
    /**
     * Genera una lista de elementos de la galería multimedia para mostrar las imágenes y sus textos.
     * @param mixed $slides
     * @return array
     */
    private final function listSlides( $slides ){
        
        return array();
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        
        $slides = $this->listSlides($widget['gallery']);
        
        $path = $this->getView('default');
        
        if(file_exists($path)){
            
            require $path;
        }
        else{
            printf('<!-- %s view not found -->',self::getWidgetId());
        }
    }
}


