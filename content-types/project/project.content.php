<?php namespace CODERS\ContentTypes;

use \CODERS\ContentBase;

/**
 * Generador de tipo de posts Proyecto para Portfolios y galerías de trabajos
 *
 * @author Jaume Llopis
 */
final class Project  extends ContentBase{
    
    
    protected final function __construct($hook = 'wp_enqueue_scripts') {
        
        
        parent::__construct($hook);
    }
    
}
