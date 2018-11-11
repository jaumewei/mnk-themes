<?php defined('ABSPATH') or die;
/**
 * Barra de navegación
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
class CodersBreadcrumbWidget extends \CODERS\WidgetBase{
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Breadcrumb' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Barra de navegaci&oacute;n de la p&aacute;gina' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersBreadcrumbWidget
     */
    protected final function registerWidgetInputs() {
        return parent::inputRegister(
                'home',self::TYPE_CHECKBOX,0,
                __('Mostrar inicio','coders_theme_manager'));
    }

    /**
     * Genera un array recursivo de los posts superiores
     * @param WP_Post $page
     * @return array
     */
    private final function listPageNodes( WP_Post $page ){
        
        $path = array( $page->ID => $page->post_title );
        
        if( $page->post_parent ){

            $parent = get_post($page->post_parent);

            $path = array( $parent->ID => $parent->post_title ) + $path;
        }
        
        return $path;
    }
    /**
     * @param WP_Post $page
     * @return array
     */
    private final function listPostNodes( WP_Post $page ){
        
        //categorías
        return array();
    }
    /**
     * @param WP_Post $post
     * @return array
     */
    private final function listNodes( WP_Post $post ){
        
        $callback = sprintf('list%sNodes', $post->post_type );
        
        return method_exists($this, $callback) ?
                //Busca por tipo de post para asociar según convenga a su ruta de navegación
                $this->$callback( $post ) :
                //invierte el orden de los elementos del array
                $this->listPostNodes($post);
    }

    /**
     * Parsea el post por tipo y extrae la ruta y genera un array
     * @param WP_Post $post
     * @param boolean $showHome
     * @return array
     */
    private final function displayPath( WP_Post $post , $showHome = FALSE ){
        
        $nodes = $this->listNodes($post);
        
        //preparación de nodos
        $path = $showHome ? array( strval( parent::__HTML('a',
                array('href'=> get_site_url(),'target'=>'_self','class' => 'home' ),
                __('Inicio','coders_theme_manager')))) : array();
        
        foreach( $nodes as $id => $title ){
            
            if( $id !== $post->ID ){
                //soy un link
                $path[] = strval( parent::__HTML('a',array(
                    'href'=> get_permalink( $id ),
                    'target'=>'_self'),$title) );
            }
            else{
                //soy el elemento final del breadcrumb
                $path[] = strval(parent::__HTML('span', array('class'=>'current'), $title));
            }
        }
        
        return $path;
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display( $instance , $args = null) {
        
        //capturar post actual, tipo, categorías y padre
        $widget = $this->inputImport($instance);
        
        $path = $this->displayPath( get_post() ,$widget['home'] );
        
        print parent::__HTML( 'div', array('class'=>'breadcrumb'), $path );
    }
}



