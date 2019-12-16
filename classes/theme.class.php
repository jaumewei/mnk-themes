<?php namespace CODERS;

use \CodersThemeManager;

/**
 * Definición del Layout del tema con diversas opciones configurables.
 * 
 * - Estructura o layout
 * 
 * - Menús del tema
 * 
 * - Sidebars del tema
 * 
 * - Otros iniciadores (logo, etc)
 * 
 */
class Theme extends \CODERS\Document{
    
    const SUPPORT_THEME_LOGO = 'logo';
    const SUPPORT_THEME_MENU = 'menu';
    const SUPPORT_THEME_SIDEBAR = 'sidebar';    
    const SUPPORT_POST_THUMBNAILS = 'post-thumbnails';
    
    const ERROR_404 = '404';
    
    const THEME_CONTENT = 'content';
    const THEME_LOGO_DEFAULT_WIDTH = 300;
    const THEME_LOGO_DEFAULT_HEIGHT = 90;
    const THEME_H1 = 'h1';
    const THEME_H2 = 'h2';
    const THEME_H3 = 'h3';
    const THEME_H4 = 'h4';
    const THEME_H5 = 'h5';
    const THEME_H6 = 'h6';
    
    const HOOK_THEME_SETUP = 'after_setup_theme';
    /**
     * @var array
     */
    private $_support = array(
        self::SUPPORT_THEME_MENU => array(),
        self::SUPPORT_THEME_SIDEBAR => array(),
        self::SUPPORT_POST_THUMBNAILS => array(),
    );
    /**
     * @var array
     */
    private $_settings = array(
        'block_class' => 'container',
        'wrapper_class' => 'wrap',
        'menu_class' => 'nav-menu',
        'sidebar_header' => self::THEME_H2,
        'logo_width' => self::THEME_LOGO_DEFAULT_WIDTH,
        'logo_height' => self::THEME_LOGO_DEFAULT_HEIGHT,
    );
    /**
     * Setup del layout
     */
    protected function __construct() {
        
        //inicializa el tema (v2)
        $this->defineThemeComponents()      //primero registra los elementos del layout
                ->initialize()              //luego inicializa el framework
                ->registerThemeSetup();     //finalmente inicializa modificaciones del tema

        parent::__construct();
    }
    /**
     * @return string
     */
    public function __toString() {
        //
        $class = get_class( $this );
        //
        $offset = strrpos( $class , 'Theme' );
        //
        return CodersThemeManager::nominalize( substr( $class , 0 , $offset ));
        //
        //return strtolower( substr( $class , 0 , $offset ) );
    }
    /**
     * @param string $name
     * @return mixed
     */
    public function __get( $name ) {
        
        if(substr($name, 0, 3) === 'id_'){
            //si es un id retorna booleano
            $this->getAsId($name);
        }
        elseif(substr($name, 0,4) === 'tag_' ){
            //si es un tag, retorna su nombre
            return $this->getTag( $name );
        }
        elseif(substr($name, 0,5) === 'wrap_' ){
            //indica si contiene un wrapper
            return $this->hasWrapper($name);
        }

        //return isset( $this->_settings[$name ] ) ? $this->_settings[$name] : '';
        return $this->get($name,'');
    }
    /**
     * @return array
     */
    protected function dump(){
        return $this->_support;
    }
    /**
     * Propiedades de la plantilla
     * @param string $attribute
     * @param mixed $default
     * @return mixed
     */
    protected final function get( $attribute , $default = null ){
        return isset( $this->_settings[ $attribute ] ) ? $this->_settings[ $attribute ] : $default;
    }
    /**
     * Establece una propiedad
     * @param string $attribute
     * @param mixed $value
     * @return \CODERS\Theme
     */
    protected final function set( $attribute , $value ){
        $this->_settings[ $attribute ] = $value;
        return $this;
    }
    /**
     * URL del estilo local
     * @param string $style
     * @return URL
     */
    protected final function getLocalStyleUrl($style) {
        
        $layout_path = sprintf( '%sassets/%s.css',$this->getThemePath(),$style);
        
        $theme_path = sprintf( '%s/%s.css',\CodersThemeManager::themePath(),$style);
        
        if(file_exists($layout_path) ){
            
            return sprintf('%sassets/%s.css',$this->getThemeUrl(),$style);
        }
        elseif(file_exists($theme_path)){
            
            sprintf('%s/%s.css', \CodersThemeManager::themeURL(),$style);
        }

        return '';
    }
    /**
     * URL del script local
     * @param string $script
     * @return URL
     */
    protected final function getLocalScriptUrl($script) {
        
        $path = sprintf( '%sassets/%s.js',$this->getThemePath(), $script );
        
        return file_exists( $path) ?
            sprintf('%sassets/%s.js',$this->getThemeUrl(),$script) :
            sprintf('%s/js/%s.js', \CodersThemeManager::themeURL(),$script);
    }
    /**
     * @param string $block
     * @return boolean
     */
    protected final function hasWrapper( $block ){
        $wrappers = $this->defineThemeWrappers();
        $container = strtolower($block);
        return in_array($container, $wrappers);
    }
    /**
     * @param string $container
     * @return string
     */
    private final function getTag( $container ){
        $tags = $this->defineThemeTags();
        return isset( $tags[$container] ) ? $tags[$container] : 'div';
    }
    /**
     * @param string $container
     * @return boolean
     */
    private final function getAsId( $container ){
        $ids = $this->defineThemeIds();
        return in_array( $container , $ids );
    }
    /**
     * Inicializa los componentes del tema:
     * - scripts
     * - estilos
     * - metas
     * - links de cabecera
     * - posiciones de menu
     * - sidebars
     * 
     * Por defecto establece los metas de compatibilidad responsive y agrega algunas clases
     * a los contenedores del tema.
     * 
     * @return \CODERS\Theme
     */
    protected function defineThemeComponents(){
        
        $scaleRestriction = array(
                    'initial-scale'=>1,
                    'maximum-scale'=>1,
                    'user-scalable'=> 'no');
        
        //Establece el estilo por defecto style.css del tema
        return $this->registerStyle('style', $this->getLocalStyleUrl('style'))
                //establece el escalado y visualización para moviles
                ->registerMeta('viewport',array( self::THEME_CONTENT => $scaleRestriction ) )
                //agregar soporte para imagen de portada en el blog
                ->registerPostThumbnail( 'post' );
    }
    /**
     * Define el formato del contenedor del sidebar
     * @return array
     */
    protected function defineSidebarContainer( $header = 'h2' ){
        
        //$header = strtolower(  $this->sidebar_header  );
        
        return array(
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title'  => sprintf('<%s class="widget-title">',$header),
            'after_title'   => sprintf('</%s>',$header),
        );
    }
    /**
     * Define la lista de características del tema (informativo para la administración)
     * @return array
     */
    protected function defineThemeFeatures(){

        $menus = count( $this->_support[self::SUPPORT_THEME_MENU]);

        $sidebars = count( $this->_support[self::SUPPORT_THEME_SIDEBAR]);

        return array(
            __('Plantilla responsive adaptable','coders_theme_manager'),
            __('Estilo personalizable','coders_theme_manager'),
            __('Imagen de portada para el Post','coders_theme_manager'),
            sprintf(__('<b>%s</b> menus y <b>%s</b> areas de widgets','coders_theme_manager'),$menus,$sidebars),
        );
    }
    /**
     * Define los bloques que incluirán wrappers para su contenido
     * @return array
     */
    protected function defineThemeWrappers(){
        return array();
    }
    /**
     * Genera la lista de bloques que incluirán su identificador como ID css
     * @return array
     */
    protected function defineThemeIds(){
        return array();
    }
    /**
     * Genera los tags especiales de los bloques del layout
     * @return array
     */
    protected function defineThemeTags( ){
        return array();
    }
    /**
     * Genera una disposición para la página.
     * Los subtemas pueden/deben sobrescribir eta función a fin de generar diferentes
     * configuraciones.
     * 
     * @return array
     */
    protected function defineThemeLayout( ){
        return array('header',self::THEME_CONTENT,'footer',);
    }
    /**
     * Define el tipo de post
     * @return array
     */
    protected function getContentType( ){
        
        $post_type = get_post_type();
        
        $content_type = array( );

        if( $post_type === false || is_404()){
            $post_type = self::ERROR_404;
            $content_type[] = 'error';
            $content_type[] = $post_type;
        }
        else{
            $content_type[] = $post_type;
        }
        
        switch( $post_type ){
            case 'page':
                $content_type[] = 'single';
                break;
            case 'post':
                $content_type[] = is_single() ? 'single' : 'loop';
                break;
            case self::ERROR_404:
            //    $post_classes[] = 'error';
                break;
            default:
                $content_type[] = 'undefined';
                break;
        }
        
        return $content_type;
    }
    /**
     * @param string $asset (opcional)
     * @return string
     */
    public final function getThemePath( $asset = NULL ){
        
        return CodersThemeManager::themePath( $asset ) ;
    }
    /**
     * @return URL
     */
    public final function getThemeUrl( ){
        return CodersThemeManager::themeURL();
    }
    /**
     * @return \CODERS\Theme
     */
    protected final function registerAdminStyle(){
        
        if(is_admin()){

            $css_path = sprintf('%s/admin.css', get_stylesheet_directory());

            if(file_exists($css_path)){

                add_action( 'admin_enqueue_scripts' , function(){

                    wp_enqueue_style(
                            'coders-admin-css',
                            sprintf('%s/admin.css', get_stylesheet_directory_uri()) );
                });
            }
        }

        return $this;
    }
    /**
     * @param string $content
     * @return string
     */
    protected function getPart( $content ){
        
        $layout_path = sprintf( '%s/html/%s.php', $this->getThemePath() , strtolower( $content ) );
        
        return file_exists($layout_path) ?
                $layout_path :
                sprintf( '%s/templates/%s.php', \CodersThemeManager::pluginPath(),$content );
    }
    /**
     * @param string $content
     * @return string
     */
    protected function getAsset( $content ){

        return sprintf( '%sassets/%s', $this->getThemePath() , strtolower( $content ) );
    }
    /**
     * @return \CODERS\Theme
     */
    protected function initialize(){

        //$menus = array();
        
        foreach( $this->_support as $type => $items ){
            switch( $type ){
                case self::SUPPORT_THEME_LOGO:
                    add_theme_support('custom-logo',array(
                        'height' => $items['height'],
                        'width' => $items['width'],
                        'flex-height' => true,
                        'flex-width' => true,
                        'header-text' => array('site-title','site-description'),
                    ));
                    break;
                case self::SUPPORT_POST_THUMBNAILS:
                    add_theme_support(self::SUPPORT_POST_THUMBNAILS,$items);
                    break;
                case self::SUPPORT_THEME_MENU:
                    register_nav_menus( $items );
                    break;
                case self::SUPPORT_THEME_SIDEBAR:
                    foreach( $items as $sidebar_id => $sidebar_content ){
                        $header = isset($sidebar_content['header']) ? $sidebar_content['header'] : 'h2';
                        register_sidebar( array_merge(
                                //ID del sidebar
                                array( 'id' => $sidebar_id ),
                                //títulos y parámetros del sidebar
                                $sidebar_content,
                                //contenedor del sidebar (definido por el tema)
                                $this->defineSidebarContainer( $header ) ) );
                    }
                    break;
            }
        }
        
        return $this;
    }
    /**
     * @param mixed $post
     * @return \CODERS\Theme
     */
    protected function registerPostThumbnail( $post ){
        //'post','page'
        if( is_array( $post ) ){
            foreach( $post as $item ){
                if( !in_array( $item , $this->_support[self::SUPPORT_POST_THUMBNAILS])){
                    $this->_support[self::SUPPORT_POST_THUMBNAILS][] = $item;
                }
            }
        }
        elseif(is_string($post)){
            if( !in_array( $post , $this->_support[self::SUPPORT_POST_THUMBNAILS])){
                $this->_support[self::SUPPORT_POST_THUMBNAILS][] = $post;
            }
        }

        return $this;
    }
    /**
     * Configura el tema
     * @return \CODERS\Theme
     */
    protected function registerThemeSetup(){

        
        return $this;
    }
    /**
     * Soporte para el logo habilitado
     * @param int $width
     * @param int $height
     * @return \CODERS\Theme
     */
    protected final function registerLogo( $width = 100 , $height = 60){
        $this->_support[ self::SUPPORT_THEME_LOGO ] = array(
            'width' => $width,
            'height' => $height,
        );
        return $this;
    }
    /**
     * @param string $menu
     * @param string $label
     * @return \CODERS\Theme
     */
    protected function registerMenu( $menu , $label ){
        if( !isset( $this->_support[self::SUPPORT_THEME_MENU][$menu])){
            $this->_support[self::SUPPORT_THEME_MENU][$menu] = $label;
        }
        return $this;
    }
    /**
     * @param string $sidebar
     * @param string $title Nombre del sidebar (en administración)
     * @param string $description Descripción en el contenedor  de sidebar del personalizador
     * @param string $header Tipo de cabecera a mostrar (H2 por defecto)
     * @return \CODERS\Theme
     */
    protected function registerSidebar( $sidebar , $title , $description = '' , $header = 'h2'){
        if( !isset( $this->_support[self::SUPPORT_THEME_SIDEBAR][$sidebar])){
            $this->_support[self::SUPPORT_THEME_SIDEBAR][$sidebar] = array(
                'name' => $title,
                'description' => strlen($description) ? $description : $this->textSidebarDescription(),
                'header' => $header,
            );
        }
        return $this;
    }
    /**
     * Define una descripción genérica para el Sidebar
     * Sobrecargar para sobrescribir el mensaje
     * @return String
     */
    protected function textSidebarDescription(){
        return __('Arrastra y suelta widgets aqu&iacute;','coders_theme_manager');
    }
    /**
     * Visualiza la página
     * @param string $template Plantilla wordpress a mostrar (experimental)
     * @return \CODERS\Theme
     */
    public final function display( $template = null ){

        //definir el nodo root del documento justo después de body
        $root = !is_null($template) ? $template : 'site-main';
        
        return $this->docHeader()
                ->displayTheme( $root , $this->defineThemeLayout() )
                ->docFooter();
    }
    /**
     * Inicio del bloque
     * @return \CODERS\Theme
     */
    private final function blockStart( $block_id ){
        if( $this->getAsId($block_id)){
            //apertura del bloque
            printf('<%s id="%s" class="%s">',
                $this->getTag( $block_id ),
                $block_id,
                $this->block_class );
        }
        else{
            //apertura del bloque
            printf('<%s class="%s %s">',
                $this->getTag( $block_id ),
                $block_id,
                $this->block_class );
        }

        if( $this->hasWrapper($block_id) ){
            //apertura del wrapper
            printf( '<div class="%s">' , is_array( $this->wrapper_class ) ? 
                    implode(' ', $this->wrapper_class) : 
                    $this->wrapper_class);
        }
        return $this;
    }
    /**
     * Finalización del bloque
     * @return \CODERS\Theme
     */
    private final function blockEnd($block_id) {

        if ($this->hasWrapper($block_id)) {
            //cierre del wrapper
            print '</div>';
        }

        //cierre del bloque
        printf('</%s>', $this->getTag($block_id));
        
        return $this;
    }
    /**
     * Muestra un bloque anidado del tema
     * @param mixed $block_id
     * @param mixed $content
     * @return \CODERS\Theme
     */
    private final function displayTheme( $block_id , $content ){
        if(is_string($block_id)){
            $this->blockStart( $block_id );
            if( is_string( $content ) ){
                $this->render( $content );
            }
            elseif( is_array( $content ) ){
                foreach ( $content as $child_block => $sub_content ) {
                    $this->displayTheme( $child_block, $sub_content );
                }
            }
            $this->blockEnd( $block_id ); 
        }
        elseif(is_numeric($block_id)){
            //solo un wrapper vacío
            $this->render($content);
        }
        return $this;
    }
    /**
     * 
     * @param type $media_id
     * @param string $size
     * @param boolean $include_meta
     */
    public function displayMedia( $media_id , $size = 'full' , $class = '' , $include_meta = true ){
        
        $att_data = wp_get_attachment_image_src( $media_id , $size );
        
        if( $att_data !== false && is_array($att_data)){

            if( !is_array($class)){
                $class = array( $class );
            }
            
            $class[] = 'media-'. is_string($size) ? strtolower( $size ) : 'custom';
            
            $url = $att_data[0];

            $width = $att_data[1];

            $height = $att_data[2];
            
            if( $width > $height ){

                $class[] = 'default';
            }
            else{

                $class[] = 'portrait';
            }
            
            if( $include_meta ){
                
                $alt = get_post_meta( $media_id, '_wp_attachment_image_alt', true );

                $title = get_post_meta( $media_id, '_wp_attachment_image_alt', true );
                
                print HTML::img($url, array(
                    'class' => implode( ' ' , $class ),
                    'alt' => $alt,
                    'title' => $title,
                ));
            }
            else{
                print HTML::img($url, array('class' => implode( ' ' , $class )));
            }
        }

        print HTML::span(null,array('class'=>'empty-media'));
    }
    /**
     * @return URL
     */
    public function getLogoUrl(){
        
        $media_id = get_theme_mod('custom_logo');
        
        $image = wp_get_attachment_image_src($media_id, 'full');
        
        return $image[0];
    }
    /**
     * Logo por defecto del tema
     * 
     * https://codex.wordpress.org/Theme_Logo
     * 
     * @param boolean $display Muestra el logo por defecto
     * @return HTML
     */
    public function displayLogo( $display = true ){

        if( $display ) {
            //métodos de impresión directa del bloque
            if( function_exists( 'the_custom_logo' ) ){
                the_custom_logo( );
            }
            else{
                printf('<a class="theme-logo" href="%s" target="_self">%s</a>' , 
                        get_site_url() ,
                        get_bloginfo( 'name' ) );
            }
        }
        else{
            //métodos de retorno del bloque como texto html
            if( function_exists( 'get_custom_logo' ) ){
                return get_custom_logo();
            }
            return sprintf('<a class="theme-logo" href="%s" target="_self">%s</a>' ,
                    get_site_url() ,
                    get_bloginfo( 'name' ) );
        }
    }
    /**
     * @param string $menu
     * @return boolean
     */
    private final function isMenu( $menu ){
        
        if( ($suffix = strrpos($menu, '-menu')) !== false ){
            $menu = substr($menu, 0 , $suffix );
            
            return isset( $this->_support[self::SUPPORT_THEME_MENU][$menu]);
        }
        
        return false;
    }
    /**
     * @param string $sidebar
     * @return boolean
     */
    private final function isSidebar( $sidebar ){

        if( ($suffix = strrpos($sidebar, '-sidebar')) !== false ){
            $sidebar = substr($sidebar, 0 , $suffix );
            
            return isset( $this->_support[self::SUPPORT_THEME_SIDEBAR][$sidebar]);
        }
        
        return false;
    }
    /**
     * Muestra un menú
     * @param string $menu
     * @param mixed $class
     * @return \CODERS\Theme
     */
    public final function displayMenu( $menu , $class = '' ){

        if( ($suffix = strrpos($menu, '-menu')) !== false ){

            $menu = substr($menu, 0 , $suffix );
        }
        
        //define personalizaciones del tema para el menu
        if( has_nav_menu( $menu ) ){

            if( !is_array($class)){
                 if(is_string($class)){
                     $class = strlen($class) ? array( $class ) : array();
                 }
                 else{
                     $class = array( strval($class) );
                 }
            }
            
            $class[] = $this->menu_class;
            $class[] = $menu . '-menu';
            $class[] = 'container';

            /*printf('<div class="nav-menu-wrapper %s %s" id="%s-menu-container">',
                    $menu,//menu en la clase
                    implode(' ', $class), //clase personalizada
                    $menu); //identificador de menu */
            print wp_nav_menu( array(
                    'theme_location' => $menu,
                    'menu_class' => implode(' ', $class),
                    'container' => FALSE,
                    'echo' => FALSE ));
            //print '</div>';
        }
        else{
            //$this->docNotFound($menu);
        }
        
        return $this;
    }
    /**
     * Muestra una barra de widgets
     * @param string $sidebar
     * @param mixed $class Clase especial para el sidebar
     * @return \CODER\Theme
     */
    public final function displaySidebar( $sidebar , $class = '' ){

        if( !is_array($class)){
             if(is_string($class)){
                 $class = strlen($class) ? array( $class ) : array();
             }
             else{
                 $class = array( strval($class) );
             }
        }

        if( ($suffix = strrpos($sidebar, '-sidebar')) !== false ){
            $sidebar = substr($sidebar, 0 , $suffix );
        }
        
        //define personalizaciones del tema para el sidebar
        if ( is_active_sidebar( $sidebar ) ) {

            printf('<div class="widget-area %s %s">',
                    $sidebar ,
                    implode(' ', $class) );

            dynamic_sidebar($sidebar);

            print '</div>';
        }
        else{
            //$this->docNotFound($sidebar);
        }
        
        return $this;
    }
    /**
     * Muestra un bloque
     * @param string $block
     * @return \CODERS\Theme
     */
    protected final function render( $block ){

        $method = sprintf('render%sBlock',$block);

        if( method_exists($this, $method)){
            //printf('<!-- custom:%s -->',$block);
            $this->$method( );
        }
        elseif( $block === 'site-logo' ){
            //printf('<!-- logo:%s -->',$block);
            $this->blockStart( $block );
            $this->displayLogo( TRUE );
            $this->blockEnd( $block );
        }
        elseif( $this->isMenu( $block ) ){
            //printf('<!-- menu:%s -->',$block);
            $this->displayMenu( $block );
        }
        elseif( $this->isSidebar( $block ) ){
            //printf('<!-- sidebar:%s -->',$block);
            $this->displaySidebar($block);
        }
        else{
            $template_part = $this->getPart($block);
            //printf('<!-- part:%s -->',$block);
            $this->blockStart( $block );
            if(file_exists($template_part)){
                require $template_part;
            }
            $this->blockEnd( $block );
        }
        
        return $this;
    }
    /**
     * Muestra el contenido de la página
     */
    protected function renderContentBlock(){
    
        $content_type = $this->getContentType();
                
        $template_name = implode( '-', $content_type );
        
        $template_path = $this->getPart( $template_name );

        printf('<div class="%s">', implode(' ', $content_type ) );

        if(file_exists($template_path)){

            require $template_path;
        }
        else{

            $this->docNotFound($template_name);
        }

        print '</div>';

    }
    /**
     * Muestra un placeholder para un contenido inexistente (menu, contenedor, sidebar ...)
     * @param string $element
     */
    protected function docNotFound( $element ){
        printf('<!-- [ %s ] %s -->', $element , __('no encontrado','coders_theme_manager'));
    }
    /**
     * @return string Título
     */
    protected function docTitle(){

        return is_front_page( /*inicio*/ ) || is_home( /*inicio o pagina de entradas*/) ?
                get_bloginfo( 'name' ) :    //solo titulo web
                get_bloginfo( 'name' ) . ' - ' . get_the_title( ); //titulo web + titulo  pagina
    }
    /**
     * @return \CODERS\Theme
     */
    protected function docHeader(){
        
        printf('<!DOCTYPE html><html %s>', get_language_attributes());

        print('<head>');
        
        printf('<title>%s</title>',$this->docTitle());

        wp_head();
        
        print('</head>');
        
        printf('<body class="%s" >', implode(' ',  get_body_class( ) ) );
        
        return $this;
    }
    /**
     * @return \CODERS\Theme
     */
    protected function docFooter(){
        
        wp_footer();
        
        print '</html>';
        
        return $this;
    }
    /**
     * @param string $theme
     * @return \CODERS\Theme
     */
    public static final function create( $theme ){
        
        $path = sprintf('%s/%s.theme.php',
                get_template_directory() ,
                strtolower( $theme ) );

        if(file_exists($path)){
            
            require_once( $path );

            $class = CodersThemeManager::classify( $theme ) . 'Theme';

            if (is_subclass_of($class, self::class)) {
                return new $class( );
            }
            else {
                //die( $class );
            }
        }
        else{
            //die($path);
        }
        //retornar siempre el layout por defecto
        return new Theme();
    }
}


