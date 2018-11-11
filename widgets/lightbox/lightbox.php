<?php defined('ABSPATH') or die;
/**
 * Lightbox para galerías
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
class CodersLightboxWidget extends \CODERS\WidgetBase{
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Lightbox' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Imagen para lightbox' , 'coders_theme_manager' );
    }
    /**
     * Registra los parámetros del widget
     * @return \CodersLightboxWidget
     */
    protected final function registerWidgetInputs() {
        return parent::registerWidgetInputs()
                ->inputRegister('media', parent::TYPE_MEDIA, 0,
                        __('Imagen','coders_theme_manager'), 
                        __('Selecciona una imagen para la galer&iacute;a','coders_theme_manager'))
                ->inputRegister('show_title', parent::TYPE_CHECKBOX, 0,
                        __('Oculta el t&iacute;tulo','coders_theme_manager'));
    }

    /**
     * Retorna el ratio del recurso multimedia según sus dimensiones
     * 
     * @param int $media_id
     */
    private final function getMediaDisplay( $media_id ){

        $meta = wp_get_attachment_metadata( $media_id );

        if( $meta !== false && isset($meta['width']) && isset($meta['height']) ){
            if( $meta['width'] < $meta['height'] ){
                return 'portrait';
            }
            elseif( $meta['width'] > $meta['height'] * 2){
                return 'landscape';
            }
        }
        return 'default';
    }
    /**
     * Genera un array de metas de la imagen a mostrar
     * @param int $media_id
     * @param string $size
     * @return array
     */
    private final function getMediaUrl( $media_id , $size = self::SIZE_THUMBNAIL ){
        
        $media_content = wp_get_attachment_image_src( $media_id , $size );

        return $media_content !== false ? $media_content[0] : '';
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        //método de display (aplicable a grande)
        $widget['media_display'] = $this->getMediaDisplay($widget['media']);
        //original
        $widget['media_url'] = $this->getMediaUrl($widget['media'], self::SIZE_FULL);

        $img = parent::__HTML('img', array(
            'src' => $widget['media_url'],
            'class' => 'lightbox-media',
        ) );
        
        if( $widget['show_title'] ){
            print $args['before_title'] . $widget['title'] . $args['after_title'];
        }
        
        print $img;
    }
}