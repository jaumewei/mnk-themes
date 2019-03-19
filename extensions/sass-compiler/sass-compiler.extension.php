<?php namespace CODERS\Extensions;

use CodersThemeManager;

/**
 * Compilador less
 */
final class SassCompiler extends \CODERS\Extension{
    
    const VERSION = 'scssphp-0.7.6';

    /**
     * Compilador Sass
     * http://leafo.net/lessphp
     * @var Leafo\ScssPhp\Compiler
     */
    private $_compiler = null;
    
    private $_input = 'style';
    
    private $_output = 'style';
    
    private $_hook = 'wp_head';

    /**
     * Compilar siempre
     * @var boolean
     */
    private $_alwaysCompile = TRUE;
    
    /**
     * @param mixed $settings
     */
    protected function __construct($settings = null) {
        
        parent::__construct($settings);
    }
    
    public final function getName() {
        return __('Compilador LESS de Servidor','coders_theme');
    }
    
    public final function getDescription() {
        return  __('Activa la compilaci&oacute;n LESS desde el servidor','coders_theme');
    }
    /**
     * 
     * @param type $settings
     * @return type
     */
    protected final function setup($settings) {
        
        /**
         * Registrar los componentes del plugin
         */
        if( $this->lib( self::VERSION. '/scss.inc') ){
        
            if ( class_exists('\Leafo\ScssPhp\Compiler') ) {

                $this->_compiler = new \Leafo\ScssPhp\Compiler();
            }
        }
        
        return parent::setup($settings);
    }
    /**
     * Registra  la compilación LESS en el hook
     * @return \CODERS\Extensions\LessCompiler
     */
    public final function init() {

        if( !is_admin( ) && !is_null($this->_compiler ) ){
            //inicializa la plantilla LESS
            //$this->initializeInputPath();

            //genera la compilación less de la plantilla
            add_action( $this->_hook , array( $this , 'queue' ), 1);
        }
        
        return parent::init();
    }
    /**
     * Comprueba que existe el directorio LESS o lo intenta crear
     * @param boolean $createIfNotExists Crear si no existe
     * @return Boolean TRUE si existe o se ha creado el directorio LESS correctamente
     */
    private final function initializeInputPath( ) {
        $path = $this->getInputPath();
        var_dump($path);
        if( !file_exists($path)){
            if( mkdir($path) ){
                file_put_contents(
                        $this->getInputPath(),
                        sprintf( '/**** %s ****/', $this->_input ) );
            }
        }
        return file_exists($this->getInputPath());
    }
    /**
     * @return string
     */
    private final function getInputPath( ){

        if( \CodersThemeManager::loaded() ){

            $layout = \CodersThemeManager::instance()->getTheme();

            return sprintf('%s/scss/%s.scss',$layout->getThemePath(),$this->_input);
        }
        
        return sprintf('%s/scss/%s.scss', CodersThemeManager::themePath(), $this->_input );
    }
    /**
     * @return string
     */
    private final function getOutputPath( ){

        return sprintf('%s/css/%s.scss.css', CodersThemeManager::themePath(), $this->_output );
    }
    /**
     * @return URL
     */
    private final function getOutputUrl( ){
        
        return sprintf('%s/css/%s.scss.css', CodersThemeManager::themeURL(), $this->_output );
    }
    /**
     * Comprueba y precompila la plantilla LESS
     * @return boolean Verdadero si hay un css de salida (existente o recientemente compilado)
     */
    private final function compile(){
        
        $less = $this->getInputPath();

        $css = $this->getOutputPath();

        if ( !is_null($this->_compiler) && file_exists( $less ) ) {

            try{

                
                
            }
            catch (Exception $ex) {
                //die($ex->getMessage());
                //reportar por la mensajería wordpress
            }
        }

        return file_exists( $css );
    }
    /**
     * Genera la lista de plantillas LESS
     */
    public final function queue( ) {
        
        if ( $this->compile( ) ) {

            //Si existe un CSS de salida, encolarlo en el hook
            wp_enqueue_style( sprintf('%s-scss',$this->_input) , $this->getOutputUrl( ) );

            return true;
        }

        return false;
    }
}
        
