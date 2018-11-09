<?php namespace CODERS;

use CodersThemeManager;

/**
 * Plantilla para extensiones
 * 
 * @author informatica1
 */
abstract class Extension{
    
    const STATUS_DISABLED = 0;
    
    const STATUS_ENABLED = 1;
    
    /**
     * @var array
     */
    private $_settings = array(
        'status' => self::STATUS_DISABLED,
    );
    
    /**
     * @param mixed $settings
     */
    protected function __construct( $settings = null ) {
        
        //
        $this->setup( $settings );
    }
    /**
     * @return string
     */
    public function __toString() {
        return CodersThemeManager::nominalize( get_class( $this ) );
    }
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        
        $method = sprintf('get%s',$name);
        
        return method_exists($this,$method) ? $this->$method() : $this->get($name,'');
    }
    /**
     * @return string
     */
    public function getName(){
        return __('Extensi&oacute;n Gen&eacute;rica','coders_theme');
    }
    /**
     * @return string
     */
    public function getDescription(){
        return __( 'Describe la funcionalidad de esta extensi&oacute;n' , 'coders_theme' );
    }
    /**
     * Código de icono a mostrar en la interfaz de administración
     * @return string
     */
    public function getUI(){ return ''; }
    /**
     * @return boolean
     */
    public function getActive(){
        
        return $this->get('status',self::STATUS_DISABLED) === self::STATUS_ENABLED;
    }
    /**
     * @return string
     */
    protected final function getPath(){
        
        return sprintf( '%s/extensions/%s/' ,
                //CodersThemeManager::themePath( ) ,
                CodersThemeManager::pluginPath( ) ,
                strval( $this ) );
    }
    /**
     * Carga librerías de la extensión
     * @param string $lib
     * @return boolean
     */
    protected final function lib( $lib ){
        
        $path = sprintf('%slib/%s.php', $this->getPath(), $lib );
        
        if(file_exists($path) ){
        
            require_once $path;
            
            return true;
        }
        
        return false;
    }
    /**
     * @return array
     */
    public final function getSettings(){
        return $this->_settings;
    }

    /**
     * @param string $att
     * @param mixed $val
     * @return \CODERS\Extension
     */
    protected function set( $att , $val ){
        $this->_settings[$att] = $val;
        return $this;
    }
    /**
     * @param string $att
     * @param mixed $default
     * @return mixed
     */
    public function get( $att , $default = null ){
        return isset( $this->_settings[$att ] ) ? $this->_settings[ $att ] : $default;
    }
    /**
     * Estado
     * @param int $status
     * @return \CODERS\Extension
     */
    public final function setStatus( $status ){
        
        $this->set('status', $status );
        
        return $this;
    }
    /**
     * Configura la extensión 
     * @param mixed $settings
     * @return \CODERS\Extensions\Extension
     */
    protected function setup( $settings ) {

        if( is_array($settings)){
            foreach( $settings as $var => $val ){
                $this->_settings[ $var ] = $val;
            }
        }
        
        return $this;
    }

    /**
     * Inicializa la extensión 
     * @return \CODERS\Extensions\Extension
     */
    public function init() {

        //

        return $this;
    }

    /**
     * Carga una extensión
     * @param string $extension
     * @return \CODERS\Extension
     * @throws Exception
     */
    public static final function import( $extension ) {

        $class = sprintf('\CODERS\Extensions\%s', CodersThemeManager::classify( $extension ) );

        $component_path = sprintf('%s/extensions/%s/%s.extension.php',
                //CodersThemeManager::themePath(),
                CodersThemeManager::pluginPath(),
                strtolower($extension),
                strtolower($extension));

        if (file_exists($component_path)) {

            require_once $component_path;

            if (class_exists($class) && is_subclass_of($class, self::class)) {

                return new $class( );
            }
            else {
                throw new \Exception(sprintf('Extension %s not found',$class) ) ;
            }
        }
        else {
            throw new \Exception(sprintf('Extension %s not found',$component_path) ) ;
        }

        return null;
    }
    /**
     * Lista los estados disponibles de la extensión
     * @return array
     */
    public static final function listStatus(){
        return array(
            self::STATUS_DISABLED => __('Desactivado','mnk_theme'),
            self::STATUS_ENABLED => __('Activado','mnk_theme'),
        );
    }
}
