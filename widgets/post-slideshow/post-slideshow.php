<?php defined('ABSPATH') or die;
/**
 * Slideshow de posts
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-05-15
 */
class CodersPostSlideshowWidget extends \CODERS\WidgetBase{
    
    const ORIENTATION_HORIZONTAL = 'horizontal';
    
    const ORIENTATION_VERTICAL = 'vertical';
    
    const THEME_DEFAULT = 'default';
    
    const THEME_CLEAR = 'clear';

    const THEME_DARK = 'dark';
    
    const CONTENT_EMPTY = 'empty';
    const CONTENT_TITLE = 'title';
    const CONTENT_TITLE_CONTENT = 'title_content';
    const CONTENT_TITLE_EXCERPT = 'title_excerpt';
    const CONTENT_ONLY_CONTENT = 'only_content';
    const CONTENT_ONLY_EXCERPT = 'only_excerpt';

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Diapositiva de Posts' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Widget CODERS de diapositivas de posts' , 'coders_theme_manager' );
    }
    /**
     * Declaración de parámetros scripts y dependencias del widget
     * @return \CodersPostSlideshowWidget
     */
    protected final function registerWidgetInputs() {

        return $this->inputRegister( 'category',
                    parent::TYPE_SELECT, '',
                    __('Selecci&oacute;n de categor&iacute;a de posts','coders_theme_manager'))
            ->inputRegister('speed',
                    parent::TYPE_FLOAT, 2.5,
                    __('Velocidad','coders_theme_manager'))
            ->inputRegister('navigator',
                    parent::TYPE_CHECKBOX, 1,
                    __('Mostrar navegaci&oacute;n','coders_theme_manager'))
            ->inputRegister('paginator',
                    parent::TYPE_CHECKBOX, 1,
                    __('Mostrar paginaci&oacute;n','coders_theme_manager'))
            /*->inputRegister('animated',
                    parent::TYPE_CHECKBOX, 1,
                    __('Activar animaci&oacute;n','coders_theme_manager'))*/
            ->inputRegister('orientation',
                    parent::TYPE_SELECT,self::ORIENTATION_HORIZONTAL,
                    __('Orientaci&oacute;n','coders_theme_manager'))
            ->inputRegister('theme',
                    parent::TYPE_SELECT,self::THEME_CLEAR,
                    __('Tema','coders_theme_manager'))
            ->inputRegister('content',
                    parent::TYPE_SELECT,self::CONTENT_TITLE_EXCERPT,
                    __('Contenido','coders_theme_manager'))
            ->inputRegister('display',
                    parent::TYPE_SELECT,'slideshow',
                    __('Apariencia','coders_theme_manager'))
            ->registerWidgetScript('post-slideshow','jquery')
            ->registerWidgetStyle('post-slideshow');
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
     * @return array
     */
    protected final function getThemeOptions(){
        return array(
            self::THEME_DEFAULT => __('Por defecto','coders_theme_manager'),
            self::THEME_CLEAR => __('Claro','coders_theme_manager'),
            self::THEME_DARK => __('Oscuro','coders_theme_manager'),
        );
    }
    /**
     * @return array
     */
    protected final function getDisplayOptions(){
        
        $output = array();
        
        $input = $this->listViewDir();
        
        foreach( array_keys($input) as $v ){
            switch( $v ){
                case 'slideshow':
                    $output[$v] = __('Diapositivas','coders_theme_manager');
                    break;
                case 'testimonials':
                    $output[$v] = __('Testimonios','coders_theme_manager');
                    break;
            }
        }
        
        return $output;
    }
    /**
     * @return array
     */
    protected final function getContentOptions(){
        return array(
            self::CONTENT_EMPTY => __('Sin texto','coders_theme_manager'),
            self::CONTENT_TITLE => __('T&iacute;tulo','coders_theme_manager'),
            self::CONTENT_ONLY_CONTENT => __('Contenido','coders_theme_manager'),
            self::CONTENT_ONLY_EXCERPT => __('Extracto','coders_theme_manager'),
            self::CONTENT_TITLE_CONTENT => __('T&iacute;tulo y Contenido','coders_theme_manager'),
            self::CONTENT_TITLE_EXCERPT => __('T&iacute;tulo y Extracto','coders_theme_manager'),
        );
    }
    /**
     * Lista los posts de la categoría seleccionada
     * @param int $category_id
     * @param boolean $full_content
     * @param string $post_type
     * @return array Lista de posts de la categoría
     */
    private final function listPosts( $category_id , $full_content = false , $post_type = 'post' ){
        
        $post_collection = array();
        
        $filters = array(
                'cat' => $category_id,
                'post_type' => $post_type,
            );

        $post_list = new WP_Query( $filters );
        
        if ( $post_list->have_posts() ){
            
            while( $post = $post_list->next_post() ){
                
                $class = array();
                
                foreach( wp_get_post_tags( $post->ID ) as $tag ){
                    $class[] = $tag->slug;
                }
                
                foreach( wp_get_post_categories( $post->ID ) as $cat ){
                    $class[] = $cat->name;
                }
                
                $class[] = $post->post_name;
                
                $post_collection[ $post->ID ] = array(
                    'title' => $post->post_title,
                    'url' => get_permalink( $post->ID ),
                    'description' => $full_content ? $post->post_content : $post->post_excerpt,
                    'image' => get_the_post_thumbnail_url( $post->ID ,'full' ),
                    'slug' => $post->post_name,    //es el SLUG
                    //'tags' => $post_tags,
                    //'categories' => $class,
                    'class' => implode(' ', $class),
                );
            }
        }
        
        return $post_collection;
    }
    /**
     * Lista de categorías disponibles
     * @return array
     */
    protected final function getCategoryOptions(){
        $list = array();

        $args = array(
            //filtros
            'hide_empty' => false
        );
        //categorías de post
        foreach( get_categories( $args ) as $category ){
            
            $list[ $category->cat_ID ] = $category->name;
        }
        
        return $list;
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display( $instance, $args = null) {

        $widget = $this->inputImport($instance);

        $full_content = $widget['content'] === self::CONTENT_TITLE_CONTENT ||
                $widget['content'] === self::CONTENT_ONLY_CONTENT;
        
        $widget['slides'] = $this->listPosts($widget['category'],$full_content);

        $widget['classes'] = sprintf('%s %s %s %s %s',
                $widget['display'] , //vista
                $widget['orientation'] , //orientación
                $widget['theme'],   //claro, oscuro
                //$widget['animated'] ? 'animated' : '',
                $widget['paginator'] ? 'has-paginator' : '',
                $widget['navigator'] ? 'has-navigator' : '');

        $path = $this->getView($widget['display']);

        if(file_exists($path)){
            require $path;
        }
        else{
            printf('<!-- %s view not found -->',self::getWidgetId());
        }
    }
}

