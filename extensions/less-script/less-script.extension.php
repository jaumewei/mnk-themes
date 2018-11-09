<?php namespace CODERS\Extensions;

use \CodersThemeManager;

/**
 * Compilador less
 */
final class LessScript extends \CODERS\Extension{

    /**
     * @var string Versión
     */
    const LESS_VERSION = '2.7.2';
    /**
     * @var URL Recurso CDN
     */
    const LESS_CDN = 'http://cdnjs.cloudflare.com/ajax/libs/less.js/%s/less.min.js';
    /**
     * @var int
     */
    private $_priority = 100;
    /**
     * @var string
     */
    private $_input = 'template';
    /**
     * @var string
     */
    private $_hook = 'wp_head';
    //private $_hook = 'wp_enqueue_scripts';
    
    
    public final function getName() {
        return __('Compilador LESS de Cliente','coders_theme');
    }
    
    public final function getDescription() {
        return  __('Activa la compilaci&oacute;n LESS de cliente por JavaScript. Genera CSS en cabecera.','coders_theme');
    }

    /**
     * Iniciador
     * @return \CODERS\Extensions\Less
     */
    public function init() {
        
        $input_name = strtolower( $this->_input );
        
        /**
         * Remplaza un tipo de enlace css por less, al estilo wordpresss ;(
         */
        add_filter( 'style_loader_tag', function( $tag ) use( $input_name ){
            
            $offset = strpos($tag, sprintf('less-%s-css', $input_name));

            if( $offset !== false ){

                return preg_replace('/text\/css/', 'text/less', $tag );
            }

            return $tag;
        } );

        /**
         * Define la url less y su script (remoto)
         */
        add_action( $this->_hook , array( $this , 'queue' ), $this->_priority );

        return parent::init();
    }
    /**
     * Encola el estilo y script less
     */
    public final function queue(){

        wp_enqueue_style( sprintf('less-%s', strtolower($this->_input) ) , $this->getInputUrl() );

        wp_enqueue_script( 'less-compiler', sprintf( self::LESS_CDN , self::LESS_VERSION ) , 'jquery');

    }
    /**
     * @return string
     */
    private final function getInputUrl( ){

        if( CodersThemeManager::loaded() ){

            $layout = CodersThemeManager::instance()->getTheme();

            return sprintf('%sless/%s.less',$layout->getThemeUrl(),$this->_input);
        }
        
        return sprintf('%s/less/%s.less', CodersThemeManager::themeURL(), $this->_input );
    }
    /**
     * Configurador
     * @return \CODERS\Extensions\Less
     */
    protected function setup( $settings ) {

        if( !is_null($settings) ){
            if(is_array($settings) ){
                foreach( $settings as $var => $val ){
                    switch( $var ){
                        case 'priority':
                            $this->_priority = intval($val);
                            break;
                    }
                }
            }
            else{
                $this->_priority = intval($settings);
            }
        }
        
        return  parent::setup($settings);
    }
}
