<?php defined('ABSPATH') or die;
/**
 */
class CodersMediaScrollWidget extends \CODERS\WidgetBase{

    const FIELD_OPTION_ORIENTATION_HORIZONTAL = 'horizontal';

    const FIELD_OPTION_ORIENTATION_VERTICAL = 'vertical';

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Scroll de im&aacute;genes' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Muestra una lista de im&aacute;genes en linea que se deslizan' , 'coders_theme_manager' );
    }
    /**
     * Declaración de parámetros scripts y dependencias del widget
     * @return \CodersMediaScrollWidget
     */
    protected final function registerWidgetInputs() {

        return $this->inputRegister('title',
                    parent::TYPE_TEXT, '',
                    __('T&iacute;tulo','coders_theme_manager'))
            ->inputRegister('gallery',
                    parent::TYPE_MEDIA_LIST, '',
                    __('Im&aacute;genes','coders_theme_manager'))
            ->inputRegister('orientation',
                    parent::TYPE_SELECT, 'horizontal',
                    __('Orientaci&oacute;n','coders_theme_manager'))
            ->inputRegister('view',
                    parent::TYPE_SELECT, 'default',
                    __('Vista personalizada','coders_theme_manager'))
            ->registerAdminScript()    //carga galería de imagenes
                ->registerWidgetScript('mediascroll')
                ->registerWidgetStyle('mediascroll');
    }
    
    /**
     * @param mixed $input
     * @return array
     */
    private final function importImageMeta( $input ){
        
        $image_list = !is_array($input) ? explode( parent::INPUT_ARRAY_SEPARATOR , $input ) : $input;
        
        $imageListMeta = array();
        
        foreach( $image_list  as $imgId ){

            if( strlen( $imgId ) && intval( $imgId ) > 0 ){
                
                $meta = get_post( $imgId );
                
                $putowordpressdemierda = wp_get_attachment_image_src($imgId,'full-size');
                
                $imageListMeta[ $imgId ] = array(
                    //'url' => wp_get_attachment_thumb_url( $meta->ID ),
                    'url' => $putowordpressdemierda[0],
                    'title' => $meta->post_title,
                    'legend' => $meta->post_excerpt,
                    'description' => $meta->post_content,
                    'alt' => get_post_meta($meta->ID,'_wp_attachment_image_alt', true));
            }
        }
        
        return $imageListMeta;
    }
    /**
     * Lista de opciones de orientación
     * @return array
     */
    protected final function getOrientationOptions(){
        return array(
                self::FIELD_OPTION_ORIENTATION_HORIZONTAL => __('Horizontal','coders_theme_manager'),
                self::FIELD_OPTION_ORIENTATION_VERTICAL => __('Vertical','coders_theme_manager'),
            );
    }
    /**
     * @return array Lista de vistas disponibles
     */
    protected final function getViewOptions(){
        
        return array_merge(
                array('default'=>__('Por defecto','coders_theme_manager')),
                $this->listThemeDir());
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected final function display($instance, $args = null) {
        
        $widget = $this->inputExtract($instance);
        
        if(strlen($widget['title'])){
            print $args['before_title'].$widget['title'].$args['after_title'];
        }
        
        $widget_path = $this->getView($widget['view']);
        
        if(file_exists($widget_path)){
            //cargar la galería para visualizar en la vista
            $gallery = $this->importImageMeta($widget['gallery']);
            //mostrar la vista
            require $widget_path;
        }
        else{
            printf( '<!-- Vista no encontrada: %s -->',$widget_path);
        }
    }
}




