<?php namespace CODERS\Extensions;

use CODERS\HTML;

/**
 * Comparte enlaces en whatsapp y otros medios mostrando el logo y descripción de la página.
 * 
 * Define TAGS OpenGraph Protocol (og)
 * 
 * Registra los metas necesarios en la cabecera para mostrar la página con su
 * logo/imagen de portada y texto descriptivo en el enlace al compartir.
 */
final class ShareUrlMeta extends \CODERS\Extension{
    
    
    public final function getName() {
        return __('Soporte OpenGraph','coders_theme');
    }
    
    public final function getDescription() {
        return  __('Comparte enlaces en whatsapp y otros medios mostrando el logo y descripción de la página.','coders_theme');
    }
    /**
     * Helper para generar los metas
     * @return array
     */
    public final function generateMetas(){
        
        $name = is_home() || is_front_page() ?
                get_bloginfo('name') :
                get_the_title();
        $description = is_home() || is_front_page() ?
                get_bloginfo('description') :
                get_the_excerpt();
        $url = is_home() || is_front_page() ?
                get_bloginfo('url') :
                get_permalink();
        $thumbnail = is_home() || is_front_page() ?
                get_theme_mod('custom_logo') :
                get_post_thumbnail_id(get_the_ID());

        $metas = array(
            HTML::meta( array( 'name' => 'og:url' , 'content' => $url ) ),
            HTML::meta( array( 'property' => 'og:type' , 'content' => 'article' ) ),
            HTML::meta( array( 'property' => 'og:locale' , 'content' => get_locale() ) ),
            HTML::meta( array( 'property' => 'og:title' , 'content' => $name ) ),
            //HTML::meta( array( 'property' => 'og:locale:alternate' , 'content' => '' ) ),
            //HTML::meta( array( 'property' => 'og:locale:alternate' , 'content' => '' ) ),
        );
        
        if( strlen( $description ) ){
            $metas[] = HTML::meta( array(
                'name' => 'og:description' ,
                'content' => $description ) );
        }
        
        if( $thumbnail ){
            $thumb_data = wp_get_attachment_image_src($thumbnail,'thumbnail');
            if( $thumb_data !== FALSE  ){
                $metas[] = HTML::meta( array(
                    'property' => 'og:image',
                    'content' => $thumb_data[ 0 ] ) );
            }
        }
        
        print implode( '', $metas);
    }
    /**
     * @param mixed $settings
     * @return \CODERS\Extensions\Extension
     */
    protected final function setup($settings) {
        
        return parent::setup($settings);
    }
    /**
     * @return \CODERS\Extensions\Extension
     */
    public final function init() {
     
        add_action( 'wp_head' , array( $this , 'generateMetas' ) );
        
        return parent::init();
    }    
}

