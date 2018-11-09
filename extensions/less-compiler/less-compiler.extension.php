<?php namespace CODERS\Extensions;

use CodersThemeManager;

/**
 * Compilador less
 */
final class LessCompiler extends \CODERS\Extension{
    /**
     * Compilador LessC
     * http://leafo.net/lessphp
     * @var \lessc
     */
    private $_compiler = null;
    
    private $_input = 'template';
    
    private $_output = 'template';
    
    private $_hook = 'wp_head';

    /**
     * Compilar siempre
     * @var boolean
     */
    private $_alwaysCompile = true;
    
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
        if( $this->lib('lessc.inc') ){
        
            if ( class_exists('\lessc') ) {

                $this->_compiler = new \lessc();
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

            return sprintf('%sless/%s.less',$layout->getThemePath(),$this->_input);
        }
        
        return sprintf('%s/less/%s.less', CodersThemeManager::themePath(), $this->_input );
    }
    /**
     * @return string
     */
    private final function getOutputPath( ){

        if( \CodersThemeManager::loaded() ){

            $layout = \CodersThemeManager::instance()->getTheme();

            return sprintf('%sassets/%s.css',$layout->getThemePath(),$this->_output);
        }
        
        return sprintf('%s/css/%s.css', CodersThemeManager::themePath(), $this->_output );
    }
    /**
     * @return URL
     */
    private final function getOutputUrl( ){
        
        if( \CodersThemeManager::loaded() ){

            $layout = \CodersThemeManager::instance()->getTheme();

            return sprintf('%sassets/%s.css',$layout->getThemeUrl(),$this->_output);
        }
        
        return sprintf('%s/css/%s.css', CodersThemeManager::themeURL(), $this->_output );
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
                $success = ($this->_alwaysCompile) ?
                        $this->_compiler->compileFile($less, $css) :
                        $this->_compiler->checkedCompile($less, $css);
                
                if(is_bool($success) && !$success){
                    //compilación condicional (boolean)
                }
                else{
                    //compilación forzada (num bytes)
                }
            }
            catch (Exception $ex) {
                die($ex->getMessage());
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
            wp_enqueue_style(CODERS_THEME_TEMPLATE_CSS, $this->getOutputUrl( ) );

            return true;
        }

        return false;
    }
}
        
        