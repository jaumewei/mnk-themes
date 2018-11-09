<?php namespace CODERS;

use CODERS\Dictionary;

/**
 * Clase abstracta para páginas de configuración
 */
class AdminPage extends Document{
    
    const ADMIN_PAGE_ACTION = 'coders_widgetman_action';
    
    const THEME_MANAGER_MENU = 'coders-theme-manager';
    
    const ADMIN_PAGE = 'coders-widget-pack';
    
    const NOTIFY_TYPE_SUCCESS = 'success';
    
    const NOTIFY_TYPE_ERROR = 'error';
    
    /**
     *
     * @var \CODERS\AdminPage[]
     */
    private static $_PAGES = [];
    /**
     * Sobrescribir el hook de carga de dependencias con el de administrador
     * CSS, JS etc, se cargan en el panel de administración
     * @var string
     */
    //protected $_hook = 'admin_enqueue_scripts';
    /**
     * @var \CODERS\Dictionary
     */
     private $_dictionary;
    /**
     * 
     */
    protected function __construct() {
        //diccionario de inputs para la página
        $this->_dictionary = new Dictionary();
        //Registra dependencias del objeto Document
        parent::__construct( 'admin_enqueue_scripts' );
        //registra los menús de administración
        $this->hook();
    }
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        
        $method = sprintf('get%s', preg_replace('/_/', '', $name));
        
        return method_exists($this, $method) ? $this->$method() : '';
    }
    /**
     * @return string
     */
    public function getUrl(){
        
        return self::form($this->getSlug());
    }
    /**
     * @return string
     */
    public function getSlug(){
        
        $page =  \CodersThemeManager::nominalize($this);
        
        return sprintf('coders-page-%s',$page);
    }
    /**
     * @return string
     */
    public function getMenuName(){
        return __('Coders Theme','coders_theme_manager');
    }
    /**
     * @return string
     */
    public function getPageName(){
        return __('Coders Theme Manager','coders_theme_manager');
    }
    /**
     * @param string $name
     * @param string $type
     * @param array $atts
     * @return \CODERS\AdminPage
     */
    protected final function addField( $name, $type, array $atts = array() ){
        $this->_dictionary->addField($name, $type , $atts );
        return $this;
    }
    /**
     * Registra una notificación para mostrar en la cabecera
     * @param string $message
     * @param string $type
     * @return \CODERS\AdminPage
     */
    public final function notify( $message , $type = 'success' ){
        
        add_action( 'admin_notices', function() use( $message , $type ){
            printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                    $type,
                    $message );
        } );
        
        return $this;
    }
    /**
     * @param string $option (opcional)
     * @return string
     */
    public static function form( $option = '' ){
        
        return sprintf('%sadmin.php?page=%s', get_admin_url(), strlen($option) ? $option : self::THEME_MANAGER_MENU);

        //$url = sprintf('%s?page=%s', get_admin_url(), self::THEME_MANAGER_MENU);
        
        //return strlen($option) ? $url . '&option=' . $option : $url;
    }
    /**
     * Comprueba si existe el menú CODERS Theme Manager instalado (tema coders v2)
     * @return boolean
     */
    public static final function isCodersThemeAdminLoaded(){
        
        return is_admin() && !empty ( $GLOBALS['admin_page_hooks'][ self::THEME_MANAGER_MENU ] );
    }
    /**
     * @return string
     */
    public function __toString() {
        
        return \CodersThemeManager::nominalize($this);
    }
    /**
     * @return \CODERS\AdminPage
     */
    protected function hook() {
        
        $adminPage = $this;
        
        add_action( 'admin_menu', function() use( $adminPage ) {

            $main_menu = \CODERS\AdminPage::THEME_MANAGER_MENU;

            if( \CODERS\AdminPage::isCodersThemeAdminLoaded() ){
                add_submenu_page( $main_menu ,
                        $adminPage->page_name,
                        $adminPage->menu_name,
                        'administrator', $adminPage->slug,
                        array($adminPage, 'display'));
            }
            else{
                $icon = 'dashicons-layout';
                $position = 50;
                add_menu_page(
                        $adminPage->page_name,
                        $adminPage->menu_name,
                        'administrator', $main_menu,
                        array($adminPage, 'display'),
                        $icon,$position);
            }
        }, 50);

        //procesa la request
        return $this->request();
    }
    /**
     * 
     */
    public function display(){
        
        printf('<div class="coders-theme-manager tab-%s">', strval($this) );
        
        $this->content();
        
        print '</div>';
    }
    /**
     * Mostrar form de widgets
     */
    protected function content(){
        
        $template_path = self::viewPath( strval($this));
        
        if(file_exists($template_path)){

            require $template_path;
        }
        else{

            $pages = array();
            
            foreach( self::$_PAGES as $page ){
                $pages[] = HTML::a(
                        $page->url,
                        $page->menu_name,
                        array('class'=>'button full-size'));
            }

            print HTML::ul($pages,array('class'=>'list-block menu clearfix columns-2'),'item');
        }
    }
    /**
     * @return \CODERS\AdminPage Resultado de la operación
     */
    protected function request(){
        
        $action = filter_input(INPUT_POST , self::ADMIN_PAGE_ACTION );
        
        if( !is_null($action)){
            $callback = sprintf('request_%s', strtolower( $action ) );
            if( method_exists( $this , $callback ) ){
                $this->$callback();
            }
        }
        
        return $this;
    }
    /**
     * Instancia una página
     * @param string $page
     * @return \CODERS\AdminPage | null
     */
    private static final function registerPage( $page ){
        
        $path = self::pagePath($page);
        
        $class = self::pageClass($page);
        
        if(file_exists($path)){
            
            require_once($path);

            if(is_subclass_of($class, self::class)){
                
                return new $class();
            }
        }
        
        return null;
    }
    /**
     * 
     */
    public static final function loadPages(){

        if( !self::isCodersThemeAdminLoaded() ){

            self::registerPage(new AdminPage());
            
            $path = sprintf('%s/admin-pages/', \CodersThemeManager::pluginPath() );

            if( is_dir( $path ) && ( $pages = scandir($path) ) !== FALSE ){

                foreach( $pages  as $p ){

                    switch( $p ){
                        case '.':
                        case '..':
                            break;
                        default:

                            if( !array_key_exists($p, self::$_PAGES) ){

                                $page = self::registerPage($p);

                                if( !is_null( $page )){

                                    self::$_PAGES[ $p ] = $page;
                                }
                            }
                            break;
                    }
                }
            }
            //registrando estilos
            add_action( 'admin_enqueue_scripts', function(){

                wp_enqueue_style('coders-admin-css',
                        sprintf('%sassets/admin.css',\CodersThemeManager::pluginURL()));
            } );
        }
    }
    /**
     * Clase y NS de una página
     * @param string $page
     * @return string
     */
    public static final function pageClass( $page ){
        
        return sprintf( '\CODERS\AdminPages\%s' ,
                \CodersThemeManager::classify($page) );
    }
    /**
     * Ruta de la vista de una página
     * @param string $page
     * @return string
     */
    public static final function viewPath( $page ){
        
        $path = sprintf('%s/admin-pages/%s/%s.html.php',
                \CodersThemeManager::pluginPath(),
                strtolower( $page ) ,
                strtolower( $page ) );
        
        return $path;
    }
    /**
     * Ruta de ubicación de una página
     * @param string $page
     * @return String
     */
    public static final function pagePath( $page ){
        return sprintf('%s/admin-pages/%s/%s.page.php',
                \CodersThemeManager::pluginPath(),
                strtolower( $page ) ,
                strtolower( $page ) );
    }
}


