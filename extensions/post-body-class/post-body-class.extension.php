<?php namespace CODERS\Extensions;

use CODERS\Request;

/**
 * Agrega clases personalizables a la página
 *
 * @author informatica1
 */
final class PostBodyClass extends \CODERS\Extension {
    
    const CFG_POSITION_SIDE = 'side';
    const CFG_PRIORITY_HIGH = 'high';
    const CFG_PRIORITY_DEFAULT = 'default';
    const CFG_PRIORITY_LOW = 'low';
    //mantener estos nombres para compatibilidad anterior
    const MBX_INPUT_NAME = 'coders_page_style_customizer';
    const MBX_INPUT_NONCE = 'coders_page_styles_admin';
    /**
     * @var array
     */
    private static $_supported = array( 'page' , 'post' );    
    /**
     * @var int
     */
    private $_priority = 1000;
    
    
    
    public final function getName() {
        return __('Clase Post CSS','coders_theme');
    }
    
    public final function getDescription() {
        return  __('Agrega soporte para clase CSS personalizada del post sobre la etiqueta BODY','coders_theme');
    }
    /**
     * Iniciador
     * @return \CODERS\Extensions\PageClass
     */
    public final function init() {
        
        
        if(is_admin()){
            //definir el cuadro  de personalización de clases en el contexto apropiado
            add_action( 'add_meta_boxes', array( $this , 'display_metabox' ) );
            //guardar cambios en administración del post
            add_action( 'save_post', array( $this , 'save_metabox' ) );
        }
        else{
            //anexar claases al body
            add_filter('body_class', array( $this , 'body_class' ), $this->_priority );
        }
        
        
        return parent::init();
    }
    /**
     * Configurador
     * @param type $settings
     * @return \CODERS\Extensions\PageClass
     */
    protected final function setup($settings) {
        
        
        
        return parent::setup($settings);
    }
    /**
     * @todo Controlar NONCES
     * @param int $post_id
     * @return type
     */
    public final function save_metabox( $post_id ){
        
        $request = new Request( );
        
        $post_type = $request->get('post_type');

        $metabox_name = $request->get(self::MBX_INPUT_NAME);

        //$nonce_name = self::nonce($post_id);

        //$nonce_value = $request->get($nonce_name);

        // Check if our nonce is set.
        /*if ( ! $request->has( $nonce_name ) ){
            //die( $nonce_name );
            return $post_id;
        }*/

        // Verify that the nonce is valid.
        /*if ( ! wp_verify_nonce( $nonce_value, $post_id ) ){
            //die( $nonce_value );
            return $post_id;
        }*/

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            //die( 'auto_save' );
            return $post_id;
        }
        
        if( !is_null($post_type) && in_array($post_type, self::$_supported )  ){
            
            switch( $post_type ){
                case 'page':
                    if ( ! current_user_can( 'edit_page', $post_id ) ) {
                        return $post_id;
                    }
                    break;
                case 'post':
                    if ( ! current_user_can( 'edit_post', $post_id ) ){
                        return $post_id;
                    }
                    break;
            }
            
        }

        /* OK, its safe for us to save the data now. */
        if( !is_null($metabox_name)){
            // Sanitize the user input.
            $value = sanitize_text_field( $metabox_name );
            // Update the meta field.
            update_post_meta( $post_id, self::MBX_INPUT_NAME, $value );
        }
    }
    /**
     * 
     * @global \CODERS\Extensions\WP_Post $post
     */
    public final function display_metabox(){
        if( is_admin() ){
            foreach ( self::$_supported as $post_type ) {

                add_meta_box(
                        'coders_page_class_customizer',
                        __( 'Clase CSS del Post', 'coders_developer_tools' ),
                        function(){
                            global $post;
                            // Add an nonce field so we can check for it later.
                            wp_nonce_field(
                                    self::MBX_INPUT_NONCE,
                                    self::nonce($post->ID));
                            $value = get_post_meta(
                                        $post->ID,
                                        self::MBX_INPUT_NAME,
                                        true );
                            printf('<p>%s</p>',__('Agregar aqu&iacute; las clases CSS necesarias <strong>separadas por espacios</strong> para permitir una personalizaci&oacute;n &uacute;nica del post.','coders_theme_manager'));
                            printf('<textarea class="widefat" name="%s" placeholder="%s" >%s</textarea>',
                                    self::MBX_INPUT_NAME,
                                    __('Clase personalizada','coders_theme_manager'),
                                    esc_attr($value));
                        },
                        $post_type, 'side', 'low'
                    );
            }
        }
    }
    /**
     * Agrega las clases al body
     * @param mixed $classes Lista de clases del filtro
     * @global WP_Post $post
     * @return array
     */
    public final function body_class( $classes ){
        
        global $post;
        
        if (isset($post)) {
            
            $custom_classes = get_post_meta(
                    $post->ID,
                    self::MBX_INPUT_NAME,
                    true );
            
            foreach( explode( ' ',$custom_classes) as $class ){

                $classes[] = strtolower( $class );
            }
        }

        return $classes;
    }
    /**
     * @param int $id
     * @return string
     */
    private static final function nonce( $id = null ){
        //return sprintf( 'post_nonce_id_%s',$id);
        return sprintf('post_nonce_id');
    }
}




