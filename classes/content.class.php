<?php namespace CODERS;

//use CODERS\Dictionary;

/**
 * Clase abstracta para generar tipos de contenido personalizado
 * No extiende la clase WP_Post, sino que actúa como un wrapper de la misma, 
 * permitiendo a wordpress trabajar con su tipo de post, facilitando desde esta
 * clase todos los elementos y herramientas que se requieren para el tipo de post
 * personalizado
 */
abstract class ContentBase extends Document{
    
    protected function __construct($hook = 'wp_enqueue_scripts') {
        
        //registrar scripts y estilos especiales del tipo de post
        
        parent::__construct($hook);
    }
    /**
     * @return string
     */
    public function __toString() {
        return CodersThemeManager::nominalize( get_class( $this ) );
    }

    public function init() {
        
    }
    
    public function setup($settings) {
        
    }
    
    /**
     * @return string
     */
    protected final function getPath(){
        
        return sprintf( '%s/content-types/%s/' ,
                //CodersThemeManager::themePath( ) ,
                CodersThemeManager::pluginPath( ) ,
                strval( $this ) );
    }
}
