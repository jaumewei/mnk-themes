<?php
/*******************************************************************************
 * Plugin Name: Coders Themes
 * Plugin URI: https://mnkcoders.com
 * Description: OOP theme helper to make theme-dev easy ;)
 * Version: 1.0.0
 * Author: Coder#1
 * Author URI: https://mnkcoders.com
 * License: GPLv2 or later
 * Text Domain: coders_themes
 * Class: CodersThemes
 * 
 * @author Coder#1
 ******************************************************************************/
final class CodersThemes {
    
    const LOAD_SCIPTS_FOOTER = true;
    
    const MEMBER_TYPE_INTERFACE = 'interface';
    const MEMBER_TYPE_CLASS = 'class';
    const MEMBER_TYPE_PUBLIC = 'public';
    const MEMBER_TYPE_ADMIN = 'admin';
    
    /**
     * @var \CodersThemes
     */
    private static $_instance = null;
    /**
     * Librerías del framework
     * @var array
     */
    private static $_libraries = array(
        'dictionary' => self::MEMBER_TYPE_CLASS,    //definición de datos y meta
        'document' => self::MEMBER_TYPE_CLASS,      //plantilla para inicializar scripts/estilos de una vista
        'theme' => self::MEMBER_TYPE_CLASS,
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
        
        
        $this->initializeFramework()            //importa librerías
                ->initializeTheme( $layout )    //registra el tema y su configuración
                ->initializeFunctions()         //Registra las funciones externas al framework
                ->initializeAdmin();            //Solo se inicializará si está en wp-admin
    }
    /**
     * 
     * @return CodersThemes
     */
    private final function initializeFunctions(){

        if( !function_exists( 'coders_thememan_image' ) ){
            /**
             * @param int|string $post_id ID del post
             * @param string $size Tamaño (por defecto thumbnail)
             * @return string
             */
            function coders_thememan_image( $post_id , $size = 'thumbnail' ){
                
                return CodersThemes::postThumbnail($post_id, $size);
            }
        }
        
        if( !function_exists('coders_thememan_hasclass')){
            /**
             * Comprueba si una clase o lista de clases está incluida en el body actual
             * @param mixed $class
             * @param boolean $exclusive método de búsqueda exclusivo para retornar solo una entre varias opciones
             * @return boolean
             */
            function coders_thememan_hasclass( $class , $exclusive = FALSE ){
                
                return CodersThemes::hasClass($class,$exclusive);
            }
        }
        
        if( !function_exists('coders_thememan_menu')){
            /**
             * Colocar un menú
             * @param string $menu
             * @param mixed $class
             */
            function coders_thememan_menu( $menu , $class = '' ){
                
                CodersThemes::menu($menu,$class);
            }
            
        }
        
        if( !function_exists('coders_thememan_sidebar')){
            /**
             * Colocar un menú
             * @param string $sidebar
             * @param mixed $class
             */
            function coders_thememan_sidebar( $sidebar , $class = '' ){
                
                CodersThemes::sidebar($sidebar,$class);
            }
            
        }
        
        if( !function_exists('coders_thememan_logo')){
            /**
             * Colocar un menú
             */
            function coders_thememan_logo( ){
                
                CodersThemes::logo();
            }
            
        }
        
        if( !function_exists('coders_thememan_index')){
            /**
             * Colocar un menú
             */
            function coders_thememan_index( $layout = null ){
                
                CodersThemes::index( $layout );
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
     * @return \ThemeCodersThemes
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
     * @return \ThemeCodersThemes
     */
    private final function initializeAdmin(){

        if (is_admin()) {

            add_action('admin_menu', function(){
                add_submenu_page(
                    'themes.php',
                    __('Coder Themes','coders_themes'),
                    __('Coder Themes', 'coders_themes'),
                    'administrator',
                    'coders-theme-manager',
                    function(){
                        //display
                        printf('<h2>%s</h2><div id="coder-theme-manager"></div>',
                                __('Coder Theme Manager','coders_themes'));


                        //debug
                        var_dump( CodersThemes::instance()->getTheme()->listExtensions() );
                        
                    }, 50);
            });
            add_action( 'admin_enqueue_scripts', function(){
                $url = CodersThemes::pluginURL();
                wp_enqueue_script( 'mnk-themes-script', sprintf('%s/assets/theme-manager.js',$url), array('jquery'), '1.0.0' );
                wp_enqueue_style( 'mnk-themes-css', sprintf('%s/assets/theme-manager.css',$url), false, '1.0.0' );
            } );
            add_action( 'wp_ajax_coder_theme_extension_list',  function(){
                
                print json_encode(array());
                
                wp_die();
            } );
            add_action( 'wp_ajax_coder_theme_extension_update',  function(){
                
                print json_encode(array());
                
                wp_die();
            } );
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
     * @return \ThemeCodersThemes
     */
    private final function initializeTheme( $layout ){
        
        if(is_null($this->_theme) ){
            
            $this->_theme = \CODERS\Theme::create( $layout );
        }
        return $this;
    }
    /**
     * @param string $extension
     * @return string
     */
    public static final function extensionPath( $extension ){
        return sprintf('%s/extensions/%s',self::pluginPath(),$extension);
    }
    /**
     * Ruta del tema (Servidor)
     * @param mixed $asset (opcional)
     * @param bool $fromChildTheme Desde el ChildTheme
     * @return String
     */
    public static final function themePath( $asset = NULL , $fromChildTheme = FALSE ){
        
        $theme_path = preg_replace('/\\\\/', '/',$fromChildTheme ?
                get_stylesheet_directory( ) :
                get_template_directory() );
        
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
        return preg_replace('/\\\\/', '/', __DIR__);
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
     * @return \CodersThemes
     */
    public static final function instance(){
        
        return self::$_instance;
    }
    /**
     * @param string $layout
     * @return \CodersThemes
     */
    public static final function initialize( ){

        if(is_null(self::$_instance)){

            self::$_instance = new CodersThemes( self::getThemeName() );
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
     * @param boolean $exclusive Cualquier clase de la lista que esté definida validará la  condición
     * @return boolean
     */
    public static final function hasClass( $class , $exclusive = FALSE ){
        
        $body_classes = get_body_class();

        if( !is_array( $class ) ){
            $class = is_string($class) ? explode(' ', $class) :  array( $class );
        }

        foreach( $class as $cls ){
            if( $exclusive && in_array($cls, $body_classes)){
                //la búsqueda exclusiva, retorna TRUE si encuentra UNO solo de los identificadores de clase
                return TRUE;
            }
            elseif( !$exclusive && !in_array($cls, $body_classes)){
                //la búsqueda inclusiva (no-exclusiva) retorna FALSE SI NO encuentra UNO de los identificadores de clase
                return FALSE;
            }
        }

        return $exclusive ?
                FALSE : //si la búsqueda es exclusiva, retorna falso al no haber encontrado ninguna de las clases
                TRUE;   //si la búsqueda es inclusiva, retorna verdadero al no haber encontrado clases faltantes
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
CodersThemes::initialize();