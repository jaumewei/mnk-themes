<?php defined('ABSPATH') or die;
/**
 * Contacto
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
final class CodersContactWidget extends \CODERS\WidgetBase {

    const WHATSAPP_API = 'https://api.whatsapp.com/send';
    
    const TYPE_EMAIL = 'mailto';
    const TYPE_TELEPHONE = 'tel';
    const TYPE_WHATSAPP = 'whatsapp';
    
    const DISPLAY_NOTHING = 0;
    const DISPLAY_CONTACT_ONLY = 1;
    const DISPLAY_TITLE_ONLY = 2;
    const DISPLAY_ALL = 3;

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Contacto' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Widget CODERS de enlace Email o tel&eacute;fono de contacto' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersContactWidget
     */
    protected final function registerWidgetInputs() {
        return parent::registerWidgetInputs() //título
                ->inputRegister( 'contact', parent::TYPE_TEXT, '',__('Contacto','coders_theme_manager'))
                ->inputRegister( 'type', parent::TYPE_SELECT, self::TYPE_EMAIL,__('Tipo','coders_theme_manager'))
                ->inputRegister( 'target', parent::TYPE_SELECT , '_self', __('Destino','coders_theme_manager'))
                ->inputRegister( 'display', parent::TYPE_SELECT , self::DISPLAY_CONTACT_ONLY , __('Mostrar','coders_theme_manager') );
    }
    /**
     * Si - No
     * @return array
     */
    protected final function getDisplayOptions(){
        return array(
            self::DISPLAY_NOTHING => __('Nada (s&oacute;lo icono)', 'coders_theme_manager'),
            self::DISPLAY_CONTACT_ONLY => __( 'S&oacute;lo contacto' , 'coders_theme_manager') ,
            self::DISPLAY_TITLE_ONLY => __( 'S&oacute;lo t&iacute;tulo' , 'coders_theme_manager') ,
            self::DISPLAY_ALL => __( 'T&iacute;tulo y contacto' , 'coders_theme_manager') );
    }
    /**
     * @return array
     */
    protected final function getTypeOptions(){
        return array(
            self::TYPE_EMAIL => __( 'Email' , 'coders_theme_manager'),
            self::TYPE_TELEPHONE => __( 'Teléfono' , 'coders_theme_manager'),
            self::TYPE_WHATSAPP => __( 'Whatsapp' , 'coders_theme_manager'),
            );
    }
    /**
     * Sanitiza el input del email
     * @param string $email
     * @return string
     */
    private final function sanitizeEmail( $email ){
        
        return preg_replace('/[^0-9a-z\.\@\-\_]/', '', trim( $email ) );
    }
    /**
     * @param string $telefono
     * @return string
     */
    private final function sanitizeTelephone( $telefono ){
        
        return preg_replace('/[^0-9\s]/', '', trim( $telefono ) );
    }
    /**
     * Valida el email
     * @param string $email
     * @return boolean
     */
    private function validateEmail( $email ) {
        
        $at = strpos('@', $email );
        
        if( $at !== false ){
            
            $dom = strrpos('.', $email);
            
            return $at < $dom && $dom < strlen($email) - 1;
        }
        
        return false;
    }
    /**
     * Filtra y sanitiza los tipos de contacto (email/telefono)
     * @param array $instance
     * @param array $old
     * @return array
     */
    protected final function inputImport(array $instance, array $old = null) {
        
        $output = parent::inputImport($instance, $old);
        
        switch( $output['type'] ){
            case self::TYPE_EMAIL:
                $output['contact'] = $this->sanitizeEmail($output['contact']);
                break;
            case self::TYPE_TELEPHONE:
            case self::TYPE_WHATSAPP:
                $output['contact'] = $this->sanitizeTelephone($output['contact']);
                break;
        }
        
        return $output;
    }
    /**
     * presentación frontal
     * @param array $instance
     * @param array $args
     */
    function display( $instance, $args = null) {

        //resuelto desde el método superior widget
        //$instance = $this->inputImport( $instance );
        
        $display = intval($instance['display']);
        
        switch( $display ){
            case self::DISPLAY_NOTHING:
                $caption = '';
                break;
            case self::DISPLAY_TITLE_ONLY:
                $caption = $instance['title'];
                break;
            default:
                $caption = $instance['contact'];
                break;
        }
        
        if( $display === self::DISPLAY_ALL && !is_null($args) ){
            //mostrando título
            print $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        
        switch( $instance ['type' ] ){
            case self::TYPE_WHATSAPP:
                print self::__HTML('a', array(
                    'href' => sprintf('%s?phone=%s' ,
                            self::WHATSAPP_API ,
                            preg_replace('/\s+/', '',  $instance['contact'] ) ),
                    'target' => $instance['target'],
                    'class' => 'icon icon-whatsapp',
                ), $caption );
                break;
            case self::TYPE_TELEPHONE:
                print self::__HTML('a', array(
                    'href' => 'tel:' . preg_replace('/\s+/', '',  $instance['contact'] ),
                    'target' => $instance['target'],
                    'class' => 'icon icon-telephone',
                ), $caption );
                break;
            case self::TYPE_EMAIL:
                print self::__HTML('a', array(
                    'href' => 'mailto:' . $instance['contact'],
                    'target' => $instance['target'],
                    'class' => 'icon icon-email',
                ), $caption );
                break;
            default:
                print '<!-- tipo de contacto inv&aacute;lido -->';
                break;
        }
    }
}
