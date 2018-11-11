<?php defined('ABSPATH') or die;
/**
 * Logo del tema
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
class CodersLogoWidget extends \CODERS\WidgetBase {
    
    const SIZE_FULL = 'full';
    const SIZE_LARGE = 'large';
    const SIZE_MEDIUM = 'medium';
    const SIZE_THUMB = 'thumbnail';

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Logo' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Logo del tema' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersSiteLogoWidget
     */
    protected final function registerWidgetInputs() {
        return $this->inputRegister('size',
                parent::TYPE_SELECT,
                self::SIZE_THUMB,
                __('Tamaño','coders_theme_manager'));
    }
    
    /**
     * Tamaños admitidos del logo
     * @return array
     */
    protected final function getSizeOptions(){
        return array(
            self::SIZE_FULL => __('Original','coders_theme_manager'),
            self::SIZE_LARGE => __('Grande','coders_theme_manager'),
            self::SIZE_MEDIUM => __('Medio','coders_theme_manager'),
            self::SIZE_THUMB => __('Pequeño','coders_theme_manager'),
        );
    }
    /**
     * Mostrar logo
     * @param array $instance
     * @param array $args
     */
    protected function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        
        $media_id = get_theme_mod('custom_logo');

        $image = wp_get_attachment_image_src($media_id, $widget['size'] );
        
        $site_title = get_bloginfo( 'title' );

        $site_desc = get_bloginfo( 'description' );
        
        print self::__HTML('img', array(
            'src' => $image[ 0 ],
            'alt' => $site_desc,
            'title' => $site_title,
        ));
    }
}


