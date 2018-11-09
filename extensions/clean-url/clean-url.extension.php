<?php namespace CODERS\Extensions;
/**
 * Agrega clases personalizables a la página
 *
 * @author informatica1
 */
final class CleanUrl extends \CODERS\Extension {
    
    /**
     * hack: add charset='utf-8' to i18n scripts
     * @param string $url
     * @return string
     */
    public function init() {
        
        add_filter('clean_url', function( $url ){

            if ( stripos($url, plugins_url('i18n/', __FILE__) !== false) ) {

                return "$url' charset='utf-8";
            }

            return $url;

        }, 11);

        return parent::init();
    }
    
    public final function getName() {
        return __('Optimizador URL UTF-8','coders_theme');
    }
    
    public final function getDescription() {
        return  __('Limpia la URL y optimiza el formato UNICODE','coders_theme');
    }
}


