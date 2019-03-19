<?php namespace CODERS\Extensions;

use CodersThemeManager;

/**
 * Descarga recursos externos a la web de CDN y terceras partes.
 * Recomendado solo para sitios de confianza
 */
final class ResourceCache extends \CODERS\Extension{
    
    function __construct($settings = null) {
        
        
        parent::__construct($settings);

    }

    
}