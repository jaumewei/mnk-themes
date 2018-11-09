<?php namespace CODERS;

use \CodersThemeManager;
use \CODERS\Html;

/**
 * Gestor/Contenedor de estilos, scripts y enlaces externos
 * 
 * Interesa más definirlo como TRAIT en lugar de clase para facilitar multi-herencia
 * 
 * @author Jaume Llopis
 */
class Document{
    /**
     * @var URL
     */
    const GOOGLE_FONTS_URL = 'https://fonts.googleapis.com/css?family';
    /**
     * @var array Scripts del componente
     */
    private $_scripts = array();
    /**
     * @var array Estilos del componente
     */
    private $_styles = array();
    /**
     * @var array Links del componente
     */
    private $_links = array();
    /**
     * @var array Metas adicionales del componente
     */
    private $_metas = array();
    
    //protected $_hook = 'wp_enqueue_scripts';
    
    protected function __construct( $hook = 'wp_enqueue_scripts') {
        
        //registra dependencias en el hook especificado
        $this->__registerAssets( $hook );
    }
    /**
     * 
     * @param string $input
     * @return boolean
     */
    public static final function containsUrl( $input ){
        
        return preg_match('/^(http|https):\/\//',$input) > 0;
    }
    /**
     * Inicializa las dependencias del componente
     * @param string $hook
     * @return \CODERS\Document
     */
    private final function __registerAssets( $hook = 'wp_enqueue_scripts' ){

        $metas = $this->_metas;
        $links = $this->_links;
        $styles = $this->_styles;
        $scripts = $this->_scripts;
        
        add_action( $hook , function() use( $metas, $links, $styles, $scripts ){

            if( !is_admin() ){
                foreach( $metas as $meta_id => $atts ){

                    print HTML::meta( $atts , $meta_id );
                }

                foreach( $links as $link_id => $atts ){
                    
                    print HTML::link(
                            $atts['href'],
                            $atts['type'],
                            array_merge( $atts, array('id'=>$link_id)));
                }
            }

            foreach( $styles as $style_id => $url ){

                wp_enqueue_style( $style_id , $url );
            }

            foreach( $scripts as $script_id => $content ){

                if( isset($content['deps']) ){
                    wp_enqueue_script( $script_id , $content['url'] , $content['deps'] , false,
                            \CodersThemeManager::LOAD_SCIPTS_FOOTER );
                }
                else{
                    wp_enqueue_script( $script_id , $content['url'] , array() , false,
                            \CodersThemeManager::LOAD_SCIPTS_FOOTER );
                }
            }
            
        });
        
        return $this;
    }
    /**
     * @return string
     */
    protected function getLocalScriptUrl( $script ){
        
        return sprintf('%sjs/%s.js', CodersThemeManager::themeURL() , $script );
    }
    /**
     * @return string
     */
    protected function getLocalStyleUrl( $style ){
        
        return sprintf('%scss/%s.css', CodersThemeManager::themeURL() , $style );
    }
    /**
     * Registra un meta en la cabecera
     * @param string $meta_id
     * @param array $attributes
     * @return \CODERS\Document
     */
    protected final function registerMeta( $meta_id , array $attributes ){
        
        if( !isset( $this->_metas[ $meta_id ] ) ){
            $this->_metas[$meta_id] = $attributes;
        }
        
        return $this;
    }
    /**
     * @param string $meta_id
     * @return \CODERS\Document
     */
    protected final function unRegisterMeta($meta_id) {
        if (isset($this->_metas[$meta_id])) {
            unset($this->_metas[$meta_id]);
        }
        return $this;
    }
    /**
     * Registra un link en la cabecera
     * @param string $link_id
     * @param string $link_url
     * @param array $attributes
     * @return \CODERS\Document
     */
    protected final function registerLink( $link_id , $link_url , array $attributes = null ){
        
        if( !isset( $this->_links[ $link_id ] ) ){
            
            if(is_null($attributes)){
                $attributes[ 'href' ] = $link_url;
            }
            else{
                $attributes[ 'href' ] = $link_url;
            }
            
            $this->_links[$link_id] = $attributes;
        }
        
        return $this;
    }
    /**
     * @param string $link_id
     * @return \CODERS\Document
     */
    protected final function unRegisterLink( $link_id ){
        if( isset( $this->_links[ $link_id ] ) ){
            unset( $this->_links[ $link_id ] );
        }
        return $this;
    }
    /**
     * Registra un estilo
     * @param string $style_id
     * @param string $style_url
     * @return \CODERS\Document
     */
    protected final function registerStyle( $style_id , $style_url ){
        
        if(strlen($style_url)){

            if( !isset( $this->_styles[ $style_id ] ) ){

                if( !self::containsUrl($style_url) ){

                    $style_url = $this->getLocalStyleUrl($style_url);
                }

                $this->_styles[$style_id] = $style_url;
            }
        }
        else{
            /**
             * @todo WARNING!!!!
             * no se ha encontrado el recurso CSS definido!!!! anotar en algún log
             */
        }
        
        return $this;
    }
    /**
     * @param string $style_id
     * @return \CODERS\Document
     */
    protected final function unRegisterStyle( $style_id ){
        if( isset( $this->_styles[ $style_id ] ) ){
            unset( $this->_styles[ $style_id ] );
        }
        return $this;
    }
    /**
     * Registra un script
     * @param string $script_id
     * @param string $script_url
     * @param mixed $deps Dependencias del script
     * @return \CODERS\Document
     */
    protected final function registerScript( $script_id , $script_url , $deps = null ){
        
        if( !isset( $this->_scripts[ $script_id ] ) ){
            
            if( !self::containsUrl($script_url) ){
                
                $script_url = $this->getLocalScriptUrl($script_url);
            }

            $this->_scripts[$script_id] = array( 'url' => $script_url );
            
            if( !is_null($deps)){
                $this->_scripts[$script_id]['deps'] = !is_array($deps) ? explode( ',', $deps ) : $deps;
            }
        }
        
        return $this;
    }
    /**
     * @param string $script_id
     * @return \CODERS\Document
     */
    protected final function unRegisterScript( $script_id ){
        if( isset( $this->_scripts[ $script_id ] ) ){
            unset( $this->_scripts[ $script_id ] );
        }
        return $this;
    }
    /**
     * Registra una fuente de Google Fonts
     * @param string $font
     * @param mixed $weight
     */
    protected final function registerGoogleFont( $font , $weight = null ){
        
        $font_id = 'font-' . preg_replace( '/ /' , '-' , strtolower($font));

        $font_url = self::GOOGLE_FONTS_URL . '=' . $font ;
        
        if( !is_null($weight)){

            if( !is_array($weight)){
 
                $weight = explode( ',' , $weight );
            }
            
            $font_url .= ':' . implode(',', $weight);
        }
        
        return $this->registerStyle( $font_id, $font_url );
    }
}


