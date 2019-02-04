<?php defined('ABSPATH') or die;
/**
 * Imagen
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
class CodersImageWidget extends \CODERS\WidgetBase{

    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_FULL = 'full';

    const TYPE_DEFAULT = 'default';
    const TYPE_ICON = 'icon';
    const TYPE_OVERLAY = 'overlay';
    const TYPE_BACKGROUND = 'background';
    const TYPE_HEADER= 'header';
    
    /**
     * Lista de fotos registradas en la navegación de la galería del lightbox
     * @var array
     */
    private static $_GALLERY = array();
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Imagen' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Imagen configurable con varias opciones' , 'coders_theme_manager' );
    }
    /**
     * Declaración de parámetros scripts y dependencias del widget
     * @return \CodersImageWidget
     */
    protected final function registerWidgetInputs() {

        return $this->inputRegister('title', parent::TYPE_TEXT, '', __('T&iacute;tulo','coders_theme_manager'))
                ->inputRegister('media', parent::TYPE_MEDIA, 0, __('Im&aacute;gen','coders_theme_manager'))
                ->inputRegister('text', parent::TYPE_TEXTAREA, '', __('Texto','coders_theme_manager'))
                ->inputRegister('link', parent::TYPE_TEXT, '', __('Enlace','coders_theme_manager'),'',array('placeholder'=>'http://'))
                ->inputRegister('view', parent::TYPE_SELECT, 'default', __('Presentaci&oacute;n','coders_theme_manager'))
                ->registerWidgetStyle('style');
    }
    
    /**
     * @return array Lista de opciones para mostrar las dimensiones de la imagen
     */
    protected final function getSizeOptions(){
        return array(
            self::SIZE_THUMBNAIL => __('Miniatura','coders_theme_manager'),
            self::SIZE_MEDIUM => __('Medio','coders_theme_manager'),
            self::SIZE_LARGE => __('Grande','coders_theme_manager'),
            self::SIZE_FULL => __('Original','coders_theme_manager'),
        );
    }
    /**
     * @return array Lista de tipos de vista a mostrar
     */
    protected final function getTypeOptions(){
        return array(
            self::TYPE_DEFAULT => __('Simple','coders_theme_manager'),
            self::TYPE_ICON => __('Icono','coders_theme_manager'),
            self::TYPE_OVERLAY => __('Superposici&oacute;n','coders_theme_manager'),
            self::TYPE_BACKGROUND => __('Imagen de fondo','coders_theme_manager'),
            self::TYPE_HEADER => __('Imagen de fondo','coders_theme_manager'),
        );
    }
    /**
     * @return array Lista de vistas disponibles
     */
    protected final function getViewOptions(){
        
        $output = $this->listViewDir();
        
        if( isset($output['default'])){
            $output['default'] = __('Vista por defecto','coders_theme_manager');
            $output['background'] = __('Imagen de fondo','coders_theme_manager');
            $output['icon'] = __('Iconizado','coders_theme_manager');
            $output['overlay'] = __('Superposici&oacute;n','coders_theme_manager');
            $output['lightbox'] = __('Lightbox','coders_theme_manager');
            $output['lightbox-gallery'] = __('Galer&iacute;a Lightbox','coders_theme_manager');
        }
        
        return array_merge( $output , $this->listThemeDir( /* importar personalizaciones del tema */) );
    }
    /**
     * @return array
     */
    protected final function listGallery(){
        return self::$_GALLERY;
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
     * Mostrar widget
     * @param array $instance
     * @param array $args
     */
    protected final function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        //método de display (aplicable a grande)
        $widget['media_display'] = $this->getMediaDisplay($widget['media']);
        //original
        $widget['media_url'] = $this->getMediaUrl($widget['media'], self::SIZE_FULL);
        //pequeña
        $widget['media_url_thumb'] = $this->getMediaUrl($widget['media'], self::SIZE_THUMBNAIL);
        //media
        $widget['media_url_medium'] = $this->getMediaUrl($widget['media'], self::SIZE_MEDIUM);
        //grande (no original)
        $widget['media_url_large'] = $this->getMediaUrl($widget['media'], self::SIZE_LARGE);
        //texto y titulo de la imagen para SEO
        $widget['media_alt'] = strlen($widget['title']) ? $widget['title'] : 'media-id-'.$widget['media'];
        //ruta de la vista
        $widget_path = $this->getView($widget['view']);

        if(file_exists($widget_path)){
            
            require $widget_path;
        }
        else{
            printf('<!-- %s -->',__('NO VIEW FOR THIS WIDGET','coders_theme_manager'));
        }
    }
    /**
     * Sobrescribir cabecera del widget (necesario para poner el tipo de vista en la clase)
     * @param array $args
     * @param array $instance
     */
    public final function widget($args, $instance) {
        
        print preg_replace('/class="/', sprintf('class="%s ',$instance['view']), $args['before_widget'] );
        
        $this->display( $instance , $args );
        
        print $args['after_widget'];        
    }
}



