<?php defined('ABSPATH') or die;
/**
 * Listado de posts
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-05-15
 */
class CodersPostsWidget extends \CODERS\WidgetBase{
    
    const POST_IMAGE_THUMBNAIL = 'thumbnail';
    const POST_IMAGE_MEDIUM = 'medium';
    const POST_IMAGE_LARGE = 'large';
    const POST_IMAGE_FULL = 'full';
    
    const DISPLAY_EMPTY = 0;
    const DISPLAY_TITLE = 1;
    const DISPLAY_CONTENT = 2;
    const DISPLAY_ALL = 3;
    
    const THEME_DEFAULT = 'default';
    const THEME_CLEAR = 'clear';
    const THEME_DARK = 'dark';

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Posts' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Muestra una lista de posts con vistas personalizables desde el tema' , 'coders_theme_manager' );
    }
    /**
     * Declaración de parámetros scripts y dependencias del widget
     * @return \CodersPostsWidget
     */
    protected final function registerWidgetInputs() {

        return $this->inputRegister('title',
                        parent::TYPE_TEXT, '',
                        __('T&iacute;tulo','coders_theme_manager'))
                ->inputRegister('category',
                        parent::TYPE_SELECT, 0,
                        __('Categor&iacute;a','coders_theme_manager'))
                ->inputRegister('count',
                        parent::TYPE_NUMBER, 4,
                        __('N&uacute;mero de posts','coders_theme_manager'))
                ->inputRegister('thumbnail',
                        parent::TYPE_SELECT, self::POST_IMAGE_THUMBNAIL ,
                        __('Formato de la imagen','coders_theme_manager'),
                        sprintf(__('Recuerda configurar los tamaños de cada tipo en <a href="%soptions-media.php" target="_blank">Ajustes</a>','coders_theme_manager'),get_admin_url()))
                ->inputRegister('theme',
                        parent::TYPE_SELECT,self::THEME_DEFAULT,
                        __('Tema','coders_theme_manager'))
                ->inputRegister( 'display',
                        parent::TYPE_SELECT, self::DISPLAY_ALL,
                        __('Mostrar contenido','coders_theme_manager'))
                ->inputRegister('view',
                        parent::TYPE_SELECT, 'default',
                        __('Vista personalizada','coders_theme_manager'))
                //Estilos
                ->registerWidgetStyle('posts');
    }
    /**
     * Lista los temas para dar  el estilo apropiado
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
     * Lista los modos de visualización de contenido
     * @return array
     */
    protected final function getDisplayOptions(){
        return array(
            self::DISPLAY_ALL => __('Mostrar todo','coders_theme_manager'),
            self::DISPLAY_CONTENT => __('Imagen y contenido','coders_theme_manager'),
            self::DISPLAY_TITLE => __('Imagen y t&iacute;tulo','coders_theme_manager'),
            self::DISPLAY_EMPTY => __('Solo imagen','coders_theme_manager'),
        );
    }
    /**
     * Lista de formatos para el thumbnail
     * @return array
     */
    protected final function getThumbnailOptions(){
        return array(
            self::POST_IMAGE_THUMBNAIL => __('Miniatura','coders_theme_manager'),
            self::POST_IMAGE_MEDIUM => __('Mediano','coders_theme_manager'),
            self::POST_IMAGE_LARGE => __('Grande','coders_theme_manager'),
            self::POST_IMAGE_FULL => __('Tamaño original','coders_theme_manager'),
        );
    }
    /**
     * Lista las categorías en un array asociado como ID => nombre categoría
     * @return array
     */
    protected final function getCategoryOptions( ){
        
        $categories = array();

        $args = array(
            'hide_empty' => false
        );

        foreach( get_categories( $args ) as $category ){
            $categories[ $category->cat_ID ] =  $category->name;
        }
        
        return $categories;
    }
    /**
     * @return array
     */
    protected final function getViewOptions() {
        
        $output = $this->listViewDir( /*Captura la lista de plantillas incluidas en el widget*/);
        
        if( isset($output['default'])){
            $output['default'] = __('Por defecto','coders_theme_manager');
        }
        
        return array_merge( $output, $this->listThemeDir(/*Plantillas personalizables (tema)*/ ) );
    }
    /**
     * Retorna el ratio del recurso multimedia según sus dimensiones
     * 
     *  - Retrato (mayor altura)
     *  - Apaisado (mayor anchura)
     *  - Cyadrado (las mismas dimensiones ancho x alto )
     * 
     * @param int $post_id
     * @return string ratio de la imagen del post
     */
    private final function getThumbDisplayClass( $post_id ){
        
        $thumbnail_id = get_post_thumbnail_id( $post_id );

        if( !empty($thumbnail_id) ){
            
            $att_meta = wp_get_attachment_metadata($thumbnail_id);
            
            if( $att_meta['width'] < $att_meta['height'] ){
                return 'portrait';
            }
            elseif( $att_meta['width'] > $att_meta['height']){
                return 'landscape';
            }
        }
        
        return 'boxed';
    }
    /**
     * @param int $category_id ID de Categoría
     * @param int $count Contador de items del post
     * @return array
     */
    private final function getPosts( $category_id , $count = 3 , $size = self::POST_IMAGE_THUMBNAIL ){
    
        $output = array();
        
        $filters = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                'posts_per_page' => $count,
                'cat' => intval( $category_id ),
            );

        $meta = new WP_Query( $filters );
        
        if ( $meta->have_posts() ){
            
            $date_format = get_option( 'date_format', 'j F, Y' );
            
            while( $post = $meta->next_post() ){
                
                $output[ $post->ID ] = array(
                    'post_title' => $post->post_title,
                    'post_url' => get_permalink( $post->ID ),
                    //'post_content' => $post->post_content, //que se lo curren y pongan el extracto
                    'post_excerpt' => $post->post_excerpt,
                    'post_thumb_url' => get_the_post_thumbnail_url( $post->ID, $size ),
                    'post_thumb_display' => $this->getThumbDisplayClass( $post->ID ),
                    //'post_category' => $post->category,
                    'post_name' => $post->post_name,    //es el SLUG
                    'post_date' => get_the_date( $date_format, $post->ID ),
                );
            }
        }

        return $output;
    }
    /**
     * Categoría o lista de categorías para mostrar en el widget (multi selección)
     * @param mixed $cat_id
     * @return array
     */
    private final function getCategories( $cat_id ){
        $output = array();
        if( is_array($cat_id)){
            
        }
        else{
            
        }
        
        return $output;
    }
    /**
     * 
     * @param type $instance
     * @param type $args
     */
    public final function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        
        $path = $this->getView($widget['view']);
        
        if(strlen($widget['title'])){
            print $args['before_title'];
            print $widget['title'];
            print $args['after_title'];
        }
        
        if(file_exists($path)){
            //clases admitidas
            $classes = array(
                'post-list-wrapper',
                $widget['theme'],
                $widget['view'],
                sprintf( 'post-items-%s', $widget['count'] ) ,
                sprintf( 'cat-id-%s', $widget['category'] ) );
            
            $categories = $this->getCategories($widget['category']);
            
            $posts = $this->getPosts( 
                    $widget['category'], 
                    $widget['count'],
                    $widget['thumbnail']);

            require $path;
        }
        else{
            printf('<!-- Vista no encontrada: %s -->',$path);
        }
    }
}


