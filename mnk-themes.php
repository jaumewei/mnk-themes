<?php
/*******************************************************************************
 * Plugin Name: Coders Theme Manager
 * Plugin URI: https://coderstheme.org
 * Description: OOP theme helper to make theme-dev easy ;)
 * Version: 1.0.0
 * Author: Jaro
 * Author URI: 
 * License: GPLv2 or later
 * Text Domain: coders_theme_manager
 * Class: CodersThemeManager
 * 
 * @author Jaume Llopis
 ******************************************************************************/
final class CodersThemeManager {
    
    const LOAD_SCIPTS_FOOTER = true;
    
    const MEMBER_TYPE_INTERFACE = 'interface';
    const MEMBER_TYPE_CLASS = 'class';
    const MEMBER_TYPE_PUBLIC = 'public';
    const MEMBER_TYPE_ADMIN = 'admin';
    
    /**
     * @var \CodersThemeManager
     */
    private static $_instance = null;
    /**
     * Librerías del framework
     * @var array
     */
    private static $_libraries = array(
        //interfaces
        //'component' => self::MEMBER_TYPE_INTERFACE, //definición de un componente iniciable/configurable
        //'response' => self::MEMBER_TYPE_INTERFACE, //definición de un componente iniciable/configurable
        //clases
        'dictionary' => self::MEMBER_TYPE_CLASS,    //definición de datos y meta
        'html' => self::MEMBER_TYPE_CLASS,          //clase base para generar HTML
        'document' => self::MEMBER_TYPE_CLASS,      //plantilla para inicializar scripts/estilos de una vista
        'theme' => self::MEMBER_TYPE_CLASS,
        'widget-base' => self::MEMBER_TYPE_CLASS,
        'widget-manager' => self::MEMBER_TYPE_CLASS,
        'extension-manager' => self::MEMBER_TYPE_CLASS,
        //'content' => self::MEMBER_TYPE_CLASS,     //post-type personalizado
        //'form' => self::MEMBER_TYPE_CLASS,        //presentación de formularios
        //'customizer' => self::MEMBER_TYPE_CLASS,    //personalizador del administrador
        'request' => self::MEMBER_TYPE_CLASS,       //captura de input GET/POST y otros
        'extension' => self::MEMBER_TYPE_CLASS,     //iniciador/configurador de extensiones y plugins
        //administrador (elementos solo para administradores)
        'admin-page' => self::MEMBER_TYPE_ADMIN,    //página de administración
    );
    /**
     * @var \CODERS\Theme
     */
    private $_theme = null;
    /**
     * Captura el momento en que se crea la instancia del manager (permite revisar optimización)
     * @var int
     */
    private $_timestamp;
    /**
     * @param string $layout
     */
    private final function __construct( $layout ) {

        $this->_timestamp = time();
        
        //importa librerías
        $this->initializeFramework();
                //de momento aqui, registrar las extensiones manualmente
                //->registerExtension('clean-url')      //parsea a formato utf8 las urls
                //->registerExtension( 'less-script' ) //compilador JS cliente
                //->registerExtension( 'share-url-meta' )//logo y titulo del sitio al compartir enlaces
                //->registerExtension( 'less-compiler' ) //compilador PHP servidor
                //->registerExtension( 'post-body-class' ) //paginas con clase en body
                //->registerExtension( 'theme-logo' ); //cargador del logo

        //importa librerías, primero inicializa extensipones, luego el layout
        $this->initializeComponents()           //inicializa los componentes registrados (extensiones, etc)
                ->initializeTheme( $layout )    //registra el tema y su configuración
                ->initializeFunctions()        //Registra las funciones externas al framework
                ->initializeAdmin();            //Solo se inicializará si está en wp-admin
    }
    /**
     * 
     * @return CodersThemeManager
     */
    private final function initializeFunctions(){

        if( !function_exists( 'coders_thememan_image' ) ){
            /**
             * @param int|string $post_id ID del post
             * @param string $size Tamaño (por defecto thumbnail)
             * @return string
             */
            function coders_thememan_image( $post_id , $size = 'thumbnail' ){
                
                return CodersThemeManager::postThumbnail($post_id, $size);
            }
        }
        
        if( !function_exists('coders_thememan_hasclass')){
            /**
             * Comprueba si una clase o lista de clases está incluida en el body actual
             * @param mixed $class
             * @return boolean
             */
            function coders_thememan_hasclass( $class ){
                
                return CodersThemeManager::hasClass($class);
            }
        }
        
        if( !function_exists('coders_thememan_menu')){
            /**
             * Colocar un menú
             * @param string $menu
             * @param mixed $class
             */
            function coders_thememan_menu( $menu , $class = '' ){
                
                CodersThemeManager::menu($menu,$class);
            }
            
        }
        
        if( !function_exists('coders_thememan_sidebar')){
            /**
             * Colocar un menú
             * @param string $sidebar
             * @param mixed $class
             */
            function coders_thememan_sidebar( $sidebar , $class = '' ){
                
                CodersThemeManager::sidebar($sidebar,$class);
            }
            
        }
        
        if( !function_exists('coders_thememan_logo')){
            /**
             * Colocar un menú
             */
            function coders_thememan_logo( ){
                
                CodersThemeManager::logo();
            }
            
        }
        
        if( !function_exists('coders_thememan_index')){
            /**
             * Colocar un menú
             */
            function coders_thememan_index( $layout = null ){
                
                CodersThemeManager::index( $layout );
            }
            
        }
        
        return $this;
    }
    /**
     * Theme seleccionado del tema
     * @return \CODERS\Theme
     */
    public final function getTheme(){
        return $this->_theme;
    }
    /**
     * @return \ThemeCodersThemeManager
     */
    private final function initializeFramework(){
        
        $framework_path = self::pluginPath( );
        
        foreach (self::$_libraries as $lib => $type ){
            switch( $type ){
                case 'interface':
                    require_once( sprintf('%s/interfaces/%s.interface.php',
                            $framework_path,
                            strtolower($lib)));
                    break;
                case 'class':
                    require_once( sprintf('%s/classes/%s.class.php',
                            $framework_path,
                            strtolower($lib)));
                    break;
                case 'public':
                    if( !is_admin() ){
                        require_once( sprintf('%s/classes/%s.class.php',
                                $framework_path,
                                strtolower( $lib ) ) );
                    } 
                    break;
                case 'admin':
                    if(is_admin() ){
                        require_once( sprintf('%s/classes/%s.class.php',
                                $framework_path,
                                strtolower( $lib ) ) );
                    } 
                    break;
            }
        }
        
        return $this;
    }
    /**
     * @return \ThemeCodersThemeManager
     */
    private final function initializeAdmin(){

        if(is_admin() && class_exists('CODERS\AdminPage')){

            \CODERS\AdminPage::loadPages();
            
        }

        return $this;
    }
    /**
     * @return \ThemeCodersThemeManager
     */
    private final function initializeComponents(){

        if(class_exists('\CODERS\ExtensionManager')){
            \CODERS\ExtensionManager::instance();
        }
        
        if(class_exists('CODERS\WidgetManager')){
            \CODERS\WidgetManager::instance();
        }
        
        return $this;
    }
    /**
     * 
     * @return string
     */
    private static final function getThemeName(){

        $theme = explode('/', preg_replace('/\\\\/', '/', get_template_directory()));

        return $theme[count($theme) - 1];
    }
    /**
     * @param string $layout
     * @return \ThemeCodersThemeManager
     */
    private final function initializeTheme( $layout ){
        
        if(is_null($this->_theme) ){
            
            $this->_theme = \CODERS\Theme::create( $layout );
        }
        return $this;
    }
    /**
     * Ruta del tema (Servidor)
     * @param mixed $asset (opcional)
     * @param bool $fromChildTheme Desde el ChildTheme
     * @return String
     */
    public static final function themePath( $asset = NULL , $fromChildTheme = FALSE ){
        
        $theme_path = $fromChildTheme ? get_stylesheet_directory( ) : get_template_directory();
        
        if(is_null($asset)){
            $asset = '';
        }
        elseif(is_array($asset)){
            $asset = implode('/', $asset );
        }
        
        return strlen( $asset ) ? sprintf( '%s/%s' , $theme_path , $asset )  : $theme_path ;
    }
    /**
     * Ruta URL del tema (Servidor)
     * @return String
     */
    public static final function themeURL( $fromChildTheme = FALSE ){
        return $fromChildTheme ? get_stylesheet_directory_uri() : get_template_directory_uri();
    }
    /**
     * Ruta del framework
     * @return string
     */
    public static final function pluginPath( ){
        return __DIR__;
    }
    /**
     * Ruta del framework
     * @return string
     */
    public static final function pluginURL( ){
        return plugin_dir_url(__FILE__);
    }
    /**
     * Ruta del tema (Servidor)
     * @deprecated since version 1 usar themePath()
     * @return String
     */
    public static final function path( ){
        return self::themePath();
    }
    /**
     * Ruta URL del tema (pública)
     * @deprecated since version 1 use themeUrl()
     * @return URL
     */
    public static final function url(){
        return self::themeURL();
    }
    /**
     * @param string $name
     * @return string
     */
    public static final function classify( $name ){
    
        $chunks = explode('-', $name);
        $output = array();
        foreach( $chunks  as $string ){
            $output[] = strtoupper( substr($string, 0,1) ) . substr($string, 1, strlen($string)-1);
        }
        return implode('', $output);

        //return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $input ) );
    }
    /**
     * Formatea un nombre de fichero como clase utilizando CamelCase
     * @param mixed $class
     * @return string
     */
    public static final function nominalize( $class ){
        
        $class_name =  is_object($class) ? get_class( $class ) : $class;
            
        if( !is_null($class_name)){

            if(is_string($class_name)){
                
                $name = explode('\\', $class_name );

                return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-',  $name[ count($name) - 1 ] ) );
            }
        }
        
        return $class_name;
    }
    /**
     * Muestra el tema
     * @param string $template Define la plantilla (página wordpress) a cargar (experimental)
     */
    public static final function index( $template = null ){
        
        if( self::loaded() ){
            
            self::$_instance->_theme->display( $template );
        }
    }
    /**
     * Define si el tema está cargado e inicializado
     * @return boolean
     */
    public static final function loaded(){
        return !is_null(self::$_instance) && !is_null( self::$_instance->_theme );
    }
    /**
     * @return \CodersThemeManager
     */
    public static final function instance(){
        
        return self::$_instance;
    }
    /**
     * @param string $layout
     * @return \CodersThemeManager
     */
    public static final function initialize( ){

        if(is_null(self::$_instance)){

            self::$_instance = new CodersThemeManager( self::getThemeName() );
        }
        
        return self::$_instance;
    }
    /**
     * Muestra el logo del sitio
     */
    public static final function logo(){
        if( self::loaded() ){
            self::instance()->_theme->displayLogo();
        }
    }
    /**
     * Muestra una imagen, agregando los REQUERIDOS descriptores del contenido multimedia que wordpress
     * no agrega ni por casualidad.
     */
    public static final function media( $media_id ){
        if( self::loaded() ){
            self::instance()->_theme->displayMedia( $media_id );
        }
    }
    /**
     * Muestra un Sidebar
     * @param string $sidebar
     * @param mixed $class Clase especial
     */
    public static final function sidebar( $sidebar , $class = '' ){
        
        if(  self::loaded() ){

            self::instance()->_theme->displaySidebar($sidebar, $class );
        }
    }
    /**
     * Muestra un Menu
     * @param string $menu
     */
    public static final function menu( $menu ){
        if(  self::loaded() ){
            self::instance()->_theme->displayMenu($menu);
        }
    }
    /**
     * Comprueba si una clase o lista de clases está incluida en el body actual
     * @param mixed $class
     * @return boolean
     */
    public static final function hasClass( $class ){
        $body_classes = get_body_class();

        if( !is_array( $class ) ){
            $class = is_string($class) ? explode(' ', $class) :  array( $class );
        }

        foreach( $class as $cls ){
            if( !in_array($cls, $body_classes)){
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * @todo Un parche, mover a algúna extensión con más control aquí no debería estar
     * @param int $post_id
     * @param mixed $size
     */
    public static final function  postThumbnail( $post_id , $size = 'thumbnail' ){
        
    }
}

//
CodersThemeManager::initialize();