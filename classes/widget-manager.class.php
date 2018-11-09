<?php namespace CODERS;

defined('ABSPATH') or die;

/**
 * Gestor de widgets
 */
final class WidgetManager{
    
    const WIDGET_LIST_OPTION = 'coders_widget_pack_widgets';
    /**
     * @var \CODERS\WidgetManager
     */
    private static $_INSTANCE = NULL;
    /**
     * Lista de widgets registrados/no-registrados
     * @var array 
     */
    private $_widgets = array();
    
    private $_updated = false;
    /**
     * 
     */
    private final function __construct() {
        
        
        $this->preload();
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        
        $callback = sprintf('get%sValue', preg_replace('/_/', '', $name));
        
        return method_exists($this,$callback) ? $this->$callback() : '';
    }
    /**
     * @return array
     */
    private static final function importSetup(){

        $import = get_option( self::WIDGET_LIST_OPTION , '' );
        
        if(strlen($import)){            

            $extract = base64_decode($import);

            $decode = json_decode($extract,TRUE);
            
            return is_array($decode) ? $decode : array();
        }
        
        return array();
    }
    /**
     * Widgets activos
     * @return int
     */
    public final function getCountActiveValue(){
        return count( $this->widgets());
    }
    /**
     * Todos los Widgets
     * @return int
     */
    public final function getCountAllValue(){
        return count( $this->widgets(FALSE));
    }
    /**
     * Cargar vista del widget.
     * Por defecto en la carpeta del widget, pero buscará primero si existe alguna vista en el tema
     * @param string $widget_id
     * @param string $view
     * @return string
     */
    public static final function widgetView( $widget_id , $view = 'default' ){
        
        return sprintf('%s/%s/html/%s.php', self::widgetPath() ,$widget_id,$view);
    }
    /**
     * Ruta del asset solicitado, estilo, script, ...
     * @param string $asset
     * @param string $widget_id
     * @return string
     */
    public static final function widgetAsset( $asset , $widget_id = null){
        return !is_null( $widget_id) ?
            //busca un asset en la carpeta del widget
            sprintf('%s/%s/assets/%s', self::widgetPath(), $widget_id, $asset ) :
            //busca un asset generico del plugin
            sprintf('%s/assets/%s', \CodersThemeManager::pluginPath() , $asset ) ;
    }
    /**
     * URL de estilos y scripts del widget, estilo, script, ...
     * @param string $asset
     * @return URL
     */
    public static final function getAssetUrl( $asset , $widget_id = null ){
        if(file_exists( self::widgetAsset($asset,$widget_id))){
            return !is_null($widget_id) ?
                //busca un asset en la carpeta del widget
                sprintf('%s/widgets/%s/assets/%s', \CodersThemeManager::pluginPath(),$widget_id,$asset) :
                //busca un asset generico del plugin
                sprintf('%sassets/%s', \CodersThemeManager::pluginURL(),$asset) ;
        }
        return '';
    }
    /**
     * @param string $widget
     * @return string
     */
    public static final function widgetPath( $widget = null ){
        
        $base_path = sprintf('%s/widgets/', \CodersThemeManager::pluginPath());
        
        return !is_null($widget) ? sprintf('%s%s/%s.php',$base_path,$widget,$widget) : $base_path;
    }
    /**
     * Comprueba que existe un widget
     * @param string $widget
     * @return boolean
     */
    public static final function checkWidget( $widget ){
        
        return file_exists( self::widgetPath( $widget ) );
    }
    /**
     * @param string $widget
     * @return string
     */
    public static final function widgetClass( $widget ){

        $classParts = explode('-', $widget);

        $class = '';

        foreach( $classParts as $chunk ){

            $class .= strtoupper( substr( $chunk , 0 , 1 ) )
                    . strtolower(substr($chunk, 1, strlen($chunk)-1));
        }
        
        return sprintf('Coders%sWidget', $class );
    }
    /**
     * @return \CODERS\WidgetManager
     */
    private final function preload(){

        $active_list = $this->importSetup();
        
        $base_path = self::widgetPath();
        
        if ( file_exists($base_path) && $handle = opendir( $base_path ) ) {

            while ( FALSE !== ( $widget = readdir($handle))){
                switch( $widget ){
                    case '.':
                    case '..':
                        break;
                    default:
                        $active = in_array($widget, $active_list);
                        if( $this->importWidget( $widget , $active ) ){
                            $this->_widgets[ $widget ] = $active;
                        }
                        break;
                }
            }
        }
        
        return $this;
    }
    /**
     * @param string $widget
     * @param boolean $register
     * @return boolean
     */
    private final function importWidget( $widget , $register = FALSE ){

        $class = self::widgetClass($widget);
        
        $path = self::widgetPath($widget);
        
        if(strlen($class) && file_exists($path)){
            
            require_once $path;
            
            if( class_exists($class) && is_subclass_of($class, WidgetBase::class ) ){
                
                if( $register ){

                    add_action( 'widgets_init', function() use( $class ){
                        /**
                         * @todo De momento así, pero no es lo suyo
                         */
                        register_widget( $class );
                    } );
                }
                
                return TRUE;
            }
        }

        return FALSE;
    }
    /**
     * @param string $widget_id
     * @param boolean $status
     * @return \CODERS\WidgetManager
     */
    public final function set( $widget_id , $status = true ){
        if( is_bool($status) && isset( $this->_widgets[$widget_id])){
            $this->_widgets[$widget_id] = $status;
            $this->_updated = TRUE;
        }
        return $this;
    }
    /**
     * Resetea (desactiva) todos los widgets activados
     * @return \CODERS\WidgetManager
     */
    public final function reset(){
        foreach( array_keys($this->_widgets ) as $widget ){
            $this->_widgets[ $widget ] = false;
        }
        $this->_updated = true;
        return $this;
    }
    /**
     * @return \CODERS\WidgetManager
     */
    public final function save(){

        if( $this->_updated){

            //exporta los widgets activos
            $export = json_encode($this->widgets());

            //guarda la lista serializada en options
            update_option( self::WIDGET_LIST_OPTION , base64_encode($export));

            $this->_updated = false;
        }
        
        return $this;
    }
    /**
     * @param boolean $activeOnly
     * @return array
     */
    public final function widgets( $activeOnly = true ){
        
        if( $activeOnly ){
            $output = array();
            foreach( $this->_widgets as $widget=>$status){
                if( $status ){
                    $output[] = $widget;
                }
            }
            return $output;
        }
        
        return $this->_widgets;
    }
    /**
     * @return array
     */
    public  final function listWidgetData(){
        
        $output = array();
        
        foreach( $this->_widgets as $widget => $status ){
            
            $class = self::widgetClass($widget);
            
            if(class_exists($class)){
                $output[$widget] = array(
                    'title' => $class::defineWidgetTitle(),
                    'description' => $class::defineWidgetDescription(),
                    'status' => $status ? 'enabled' : 'disabled',
                    );
            }
        }
        
        return $output;
    }
    /**
     * @return \CODERS\WidgetManager
     */
    public static final function instance(){
        
        if(is_null(self::$_INSTANCE)){
            
            self::$_INSTANCE = new WidgetManager();
        }
        
        return self::$_INSTANCE;
    }
}

